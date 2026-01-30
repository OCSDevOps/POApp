<?php

namespace App\Services\Integrations;

use App\Models\AccountingIntegration;
use App\Models\IntegrationSyncLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseIntegrationService
{
    protected AccountingIntegration $integration;
    protected ?IntegrationSyncLog $currentLog = null;

    public function __construct(AccountingIntegration $integration)
    {
        $this->integration = $integration;
    }

    /**
     * Test the connection to the accounting system.
     */
    abstract public function testConnection(): bool;

    /**
     * Refresh the OAuth access token.
     */
    abstract public function refreshAccessToken(): bool;

    /**
     * Export a purchase order to the accounting system.
     */
    abstract public function exportPurchaseOrder($purchaseOrder): array;

    /**
     * Export a vendor/supplier to the accounting system.
     */
    abstract public function exportVendor($supplier): array;

    /**
     * Export an item to the accounting system.
     */
    abstract public function exportItem($item): array;

    /**
     * Import vendors from the accounting system.
     */
    abstract public function importVendors(): array;

    /**
     * Import items from the accounting system.
     */
    abstract public function importItems(): array;

    /**
     * Start a sync log entry.
     */
    protected function startSyncLog(string $syncType, string $operation, ?string $entityType = null, ?int $entityId = null): IntegrationSyncLog
    {
        $this->currentLog = IntegrationSyncLog::create([
            'integration_id' => $this->integration->id,
            'company_id' => $this->integration->company_id,
            'sync_type' => $syncType,
            'operation' => $operation,
            'status' => 'pending',
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'started_at' => now(),
        ]);

        return $this->currentLog;
    }

    /**
     * Complete a sync log entry.
     */
    protected function completeSyncLog(
        string $status,
        int $attempted = 0,
        int $succeeded = 0,
        int $failed = 0,
        ?string $externalId = null,
        ?string $errorMessage = null,
        ?array $errorDetails = null
    ): void {
        if (!$this->currentLog) {
            return;
        }

        $completedAt = now();
        $duration = $this->currentLog->started_at->diffInSeconds($completedAt);

        $this->currentLog->update([
            'status' => $status,
            'records_attempted' => $attempted,
            'records_succeeded' => $succeeded,
            'records_failed' => $failed,
            'external_id' => $externalId,
            'error_message' => $errorMessage,
            'error_details' => $errorDetails,
            'completed_at' => $completedAt,
            'duration_seconds' => $duration,
        ]);

        $this->currentLog = null;
    }

    /**
     * Make an authenticated API request.
     */
    protected function makeRequest(string $method, string $url, array $data = [], array $headers = []): array
    {
        // Check if token needs refresh
        if ($this->integration->isTokenExpired()) {
            if (!$this->refreshAccessToken()) {
                throw new \Exception('Failed to refresh access token');
            }
        }

        $defaultHeaders = [
            'Authorization' => 'Bearer ' . decrypt($this->integration->access_token),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $headers = array_merge($defaultHeaders, $headers);

        try {
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->{strtolower($method)}($url, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                    'status_code' => $response->status(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Integration API request failed', [
                'integration_id' => $this->integration->id,
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Map field values using custom field mappings.
     */
    protected function mapFields(string $entityType, array $data): array
    {
        $mappings = $this->integration->fieldMappings()
            ->where('entity_type', $entityType)
            ->get();

        if ($mappings->isEmpty()) {
            return $data;
        }

        $mapped = [];
        foreach ($mappings as $mapping) {
            if (isset($data[$mapping->internal_field])) {
                $value = $data[$mapping->internal_field];
                $transformedValue = $mapping->transform($value);
                $mapped[$mapping->external_field] = $transformedValue;
            }
        }

        // Include unmapped fields
        foreach ($data as $key => $value) {
            if (!$mappings->where('internal_field', $key)->count()) {
                $mapped[$key] = $value;
            }
        }

        return $mapped;
    }

    /**
     * Handle API errors and log them.
     */
    protected function handleError(string $operation, \Exception $e): void
    {
        Log::error("Integration error: {$operation}", [
            'integration_id' => $this->integration->id,
            'integration_type' => $this->integration->integration_type,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        if ($this->currentLog) {
            $this->completeSyncLog(
                status: 'failed',
                errorMessage: $e->getMessage(),
                errorDetails: ['trace' => $e->getTraceAsString()]
            );
        }
    }
}
