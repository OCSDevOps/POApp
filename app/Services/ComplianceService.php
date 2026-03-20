<?php

namespace App\Services;

use App\Models\SupplierCompliance;
use App\Models\Contract;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ComplianceService
{
    /**
     * Add a compliance item for a supplier.
     */
    public function addComplianceItem(int $supplierId, array $data, $file = null): array
    {
        try {
            $item = new SupplierCompliance();
            $item->compliance_supplier_id = $supplierId;
            $item->compliance_type = $data['compliance_type'];
            $item->compliance_name = $data['compliance_name'];
            $item->compliance_number = $data['compliance_number'] ?? null;
            $item->compliance_issuer = $data['compliance_issuer'] ?? null;
            $item->compliance_amount = $data['compliance_amount'] ?? null;
            $item->compliance_issue_date = $data['compliance_issue_date'] ?? null;
            $item->compliance_expiry_date = $data['compliance_expiry_date'] ?? null;
            $item->compliance_warning_days = $data['compliance_warning_days'] ?? 30;
            $item->compliance_required = $data['compliance_required'] ?? true;
            $item->compliance_contract_id = $data['compliance_contract_id'] ?? null;
            $item->compliance_notes = $data['compliance_notes'] ?? null;
            $item->compliance_created_by = auth()->id();
            $item->compliance_created_at = now();
            $item->company_id = session('company_id');

            // Determine initial status based on expiry
            $item->compliance_status = $this->calculateStatus($item->compliance_expiry_date, $item->compliance_warning_days);

            if ($file) {
                $stored = $this->storeDocument($file, $supplierId);
                $item->compliance_document_path = $stored['path'];
                $item->compliance_document_name = $stored['name'];
            }

            $item->save();

            return ['success' => true, 'item' => $item];
        } catch (\Exception $e) {
            Log::error('Compliance item creation failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Update a compliance item.
     */
    public function updateComplianceItem(int $complianceId, array $data, $file = null): array
    {
        try {
            $item = SupplierCompliance::findOrFail($complianceId);

            $item->fill($data);
            $item->compliance_modified_by = auth()->id();
            $item->compliance_modified_at = now();

            // Recalculate status
            $item->compliance_status = $this->calculateStatus(
                $item->compliance_expiry_date,
                $item->compliance_warning_days
            );

            if ($file) {
                // Remove old document
                if ($item->compliance_document_path) {
                    Storage::disk('public')->delete($item->compliance_document_path);
                }

                $stored = $this->storeDocument($file, $item->compliance_supplier_id);
                $item->compliance_document_path = $stored['path'];
                $item->compliance_document_name = $stored['name'];
            }

            $item->save();

            return ['success' => true, 'item' => $item];
        } catch (\Exception $e) {
            Log::error('Compliance item update failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get all items expiring within N days.
     */
    public function getExpiringItems(int $days = 30)
    {
        return SupplierCompliance::active()
            ->expiringSoon($days)
            ->with('supplier')
            ->orderBy('compliance_expiry_date')
            ->get();
    }

    /**
     * Get all expired items.
     */
    public function getExpiredItems()
    {
        return SupplierCompliance::active()
            ->expired()
            ->with('supplier')
            ->orderBy('compliance_expiry_date')
            ->get();
    }

    /**
     * Get compliance status summary for a supplier.
     */
    public function getSupplierComplianceStatus(int $supplierId): array
    {
        $items = SupplierCompliance::bySupplier($supplierId)
            ->active()
            ->get();

        $expired = $items->filter(fn($i) => $i->is_expired);
        $expiring = $items->filter(fn($i) => $i->is_expiring_soon);
        $missingRequired = $this->getMissingRequiredTypes($supplierId, $items);

        return [
            'is_compliant' => $expired->isEmpty() && empty($missingRequired),
            'items' => $items,
            'expired' => $expired,
            'expiring_soon' => $expiring,
            'missing_required' => $missingRequired,
            'total' => $items->count(),
        ];
    }

    /**
     * Check if a contract's supplier has current required compliance.
     */
    public function checkContractCompliance(int $contractId): array
    {
        $contract = Contract::findOrFail($contractId);
        return $this->getSupplierComplianceStatus($contract->contract_supplier_id);
    }

    /**
     * Batch update compliance statuses based on expiry dates.
     */
    public function updateComplianceStatuses(): int
    {
        $updated = 0;

        $items = SupplierCompliance::active()->whereNotNull('compliance_expiry_date')->get();

        foreach ($items as $item) {
            $newStatus = $this->calculateStatus($item->compliance_expiry_date, $item->compliance_warning_days);

            if ($item->compliance_status !== $newStatus) {
                $item->compliance_status = $newStatus;
                $item->save();
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Get dashboard data for the compliance overview.
     */
    public function getDashboardData(): array
    {
        $expired = $this->getExpiredItems();
        $expiring = $this->getExpiringItems(30);

        $totalActive = SupplierCompliance::active()->count();
        $totalRequired = SupplierCompliance::active()->required()->count();

        return [
            'expired_items' => $expired,
            'expiring_items' => $expiring,
            'expired_count' => $expired->count(),
            'expiring_count' => $expiring->count(),
            'total_active' => $totalActive,
            'total_required' => $totalRequired,
        ];
    }

    /**
     * Calculate compliance status based on expiry date.
     */
    private function calculateStatus($expiryDate, int $warningDays = 30): int
    {
        if (!$expiryDate) {
            return SupplierCompliance::STATUS_CURRENT;
        }

        $expiry = $expiryDate instanceof \Carbon\Carbon ? $expiryDate : \Carbon\Carbon::parse($expiryDate);

        if ($expiry->isPast()) {
            return SupplierCompliance::STATUS_EXPIRED;
        }

        if ($expiry->lte(now()->addDays($warningDays))) {
            return SupplierCompliance::STATUS_EXPIRING_SOON;
        }

        return SupplierCompliance::STATUS_CURRENT;
    }

    /**
     * Store a compliance document file.
     */
    private function storeDocument($file, int $supplierId): array
    {
        $originalName = $file->getClientOriginalName();
        $storedName = Str::random(10) . '_' . $originalName;
        $path = $file->storeAs("compliance/{$supplierId}", $storedName, 'public');

        return [
            'path' => $path,
            'name' => $originalName,
        ];
    }

    /**
     * Get missing required compliance types for a supplier.
     */
    private function getMissingRequiredTypes(int $supplierId, $existingItems): array
    {
        // Define standard required types
        $requiredTypes = [
            SupplierCompliance::TYPE_GENERAL_LIABILITY,
            SupplierCompliance::TYPE_WORKERS_COMP,
        ];

        $existingTypes = $existingItems
            ->where('compliance_required', true)
            ->pluck('compliance_type')
            ->unique()
            ->toArray();

        $missing = [];
        $typeOptions = SupplierCompliance::getTypeOptions();

        foreach ($requiredTypes as $type) {
            if (!in_array($type, $existingTypes)) {
                $missing[] = $typeOptions[$type] ?? $type;
            }
        }

        return $missing;
    }
}
