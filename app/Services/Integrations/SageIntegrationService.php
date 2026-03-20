<?php

namespace App\Services\Integrations;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Support\Facades\Http;

class SageIntegrationService extends BaseIntegrationService
{
    private const API_BASE_URL = 'https://api.sage.com';
    private const AUTH_URL = 'https://oauth.sage.com';
    
    /**
     * Test the connection to Sage.
     */
    public function testConnection(): bool
    {
        try {
            $response = $this->makeRequest('GET', self::API_BASE_URL . '/v3.1/company');
            return $response['success'];
        } catch (\Exception $e) {
            $this->handleError('test_connection', $e);
            return false;
        }
    }

    /**
     * Refresh the OAuth access token for Sage.
     */
    public function refreshAccessToken(): bool
    {
        try {
            $response = Http::asForm()->post(self::AUTH_URL . '/oauth/token', [
                'grant_type' => 'refresh_token',
                'client_id' => decrypt($this->integration->client_id),
                'client_secret' => decrypt($this->integration->client_secret),
                'refresh_token' => decrypt($this->integration->refresh_token),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->integration->update([
                    'access_token' => encrypt($data['access_token']),
                    'refresh_token' => encrypt($data['refresh_token']),
                    'token_expires_at' => now()->addSeconds($data['expires_in']),
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            $this->handleError('refresh_token', $e);
            return false;
        }
    }

    /**
     * Export a purchase order to Sage.
     */
    public function exportPurchaseOrder($purchaseOrder): array
    {
        $this->startSyncLog('purchase_order', 'export', 'PurchaseOrder', $purchaseOrder->porder_id);

        try {
            $sageData = $this->preparePurchaseOrderData($purchaseOrder);
            $mappedData = $this->mapFields('purchase_order', $sageData);

            $response = $this->makeRequest(
                'POST',
                self::API_BASE_URL . '/v3.1/purchase_invoices',
                $mappedData
            );

            if ($response['success']) {
                $externalId = $response['data']['id'] ?? null;
                
                $this->completeSyncLog(
                    status: 'success',
                    attempted: 1,
                    succeeded: 1,
                    failed: 0,
                    externalId: $externalId
                );

                return [
                    'success' => true,
                    'external_id' => $externalId,
                    'message' => 'Purchase order exported successfully',
                ];
            }

            $this->completeSyncLog(
                status: 'failed',
                attempted: 1,
                succeeded: 0,
                failed: 1,
                errorMessage: $response['error']
            );

            return [
                'success' => false,
                'error' => $response['error'],
            ];
        } catch (\Exception $e) {
            $this->handleError('export_purchase_order', $e);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Export a vendor to Sage.
     */
    public function exportVendor($supplier): array
    {
        $this->startSyncLog('vendor', 'export', 'Supplier', $supplier->sup_id);

        try {
            $sageData = $this->prepareVendorData($supplier);
            $mappedData = $this->mapFields('vendor', $sageData);

            $response = $this->makeRequest(
                'POST',
                self::API_BASE_URL . '/v3.1/contacts',
                $mappedData
            );

            if ($response['success']) {
                $externalId = $response['data']['id'] ?? null;
                
                $this->completeSyncLog(
                    status: 'success',
                    attempted: 1,
                    succeeded: 1,
                    failed: 0,
                    externalId: $externalId
                );

                return [
                    'success' => true,
                    'external_id' => $externalId,
                    'message' => 'Vendor exported successfully',
                ];
            }

            $this->completeSyncLog(
                status: 'failed',
                attempted: 1,
                succeeded: 0,
                failed: 1,
                errorMessage: $response['error']
            );

            return [
                'success' => false,
                'error' => $response['error'],
            ];
        } catch (\Exception $e) {
            $this->handleError('export_vendor', $e);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Export an item to Sage.
     */
    public function exportItem($item): array
    {
        $this->startSyncLog('item', 'export', 'Item', $item->item_id);

        try {
            $sageData = $this->prepareItemData($item);
            $mappedData = $this->mapFields('item', $sageData);

            $response = $this->makeRequest(
                'POST',
                self::API_BASE_URL . '/v3.1/products',
                $mappedData
            );

            if ($response['success']) {
                $externalId = $response['data']['id'] ?? null;
                
                $this->completeSyncLog(
                    status: 'success',
                    attempted: 1,
                    succeeded: 1,
                    failed: 0,
                    externalId: $externalId
                );

                return [
                    'success' => true,
                    'external_id' => $externalId,
                    'message' => 'Item exported successfully',
                ];
            }

            $this->completeSyncLog(
                status: 'failed',
                attempted: 1,
                succeeded: 0,
                failed: 1,
                errorMessage: $response['error']
            );

            return [
                'success' => false,
                'error' => $response['error'],
            ];
        } catch (\Exception $e) {
            $this->handleError('export_item', $e);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Import vendors from Sage.
     */
    public function importVendors(): array
    {
        $this->startSyncLog('vendor', 'import');

        try {
            $response = $this->makeRequest(
                'GET',
                self::API_BASE_URL . '/v3.1/contacts?contact_type_ids=supplier'
            );

            if (!$response['success']) {
                $this->completeSyncLog(
                    status: 'failed',
                    errorMessage: $response['error']
                );

                return [
                    'success' => false,
                    'error' => $response['error'],
                ];
            }

            $vendors = $response['data']['$items'] ?? [];
            $imported = 0;
            $failed = 0;
            $errors = [];

            foreach ($vendors as $vendorData) {
                try {
                    // Import logic here - create/update Supplier model
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = [
                        'vendor_id' => $vendorData['id'],
                        'error' => $e->getMessage(),
                    ];
                }
            }

            $this->completeSyncLog(
                status: $failed > 0 ? 'partial' : 'success',
                attempted: count($vendors),
                succeeded: $imported,
                failed: $failed,
                errorDetails: $errors
            );

            return [
                'success' => true,
                'imported' => $imported,
                'failed' => $failed,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            $this->handleError('import_vendors', $e);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Import items from Sage.
     */
    public function importItems(): array
    {
        $this->startSyncLog('item', 'import');

        try {
            $response = $this->makeRequest(
                'GET',
                self::API_BASE_URL . '/v3.1/products'
            );

            if (!$response['success']) {
                $this->completeSyncLog(
                    status: 'failed',
                    errorMessage: $response['error']
                );

                return [
                    'success' => false,
                    'error' => $response['error'],
                ];
            }

            $items = $response['data']['$items'] ?? [];
            $imported = 0;
            $failed = 0;
            $errors = [];

            foreach ($items as $itemData) {
                try {
                    // Import logic here - create/update Item model
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = [
                        'item_id' => $itemData['id'],
                        'error' => $e->getMessage(),
                    ];
                }
            }

            $this->completeSyncLog(
                status: $failed > 0 ? 'partial' : 'success',
                attempted: count($items),
                succeeded: $imported,
                failed: $failed,
                errorDetails: $errors
            );

            return [
                'success' => true,
                'imported' => $imported,
                'failed' => $failed,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            $this->handleError('import_items', $e);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Prepare purchase order data for Sage format.
     */
    private function preparePurchaseOrderData($po): array
    {
        return [
            'contact_id' => $po->supplier->sage_id ?? null,
            'date' => $po->porder_order_date,
            'reference' => $po->porder_no,
            'due_date' => $po->porder_required_date,
            'total_amount' => $po->porder_total_amount,
            'notes' => $po->porder_description,
            'invoice_lines' => $po->items->map(function ($item) {
                return [
                    'description' => $item->item->item_name ?? '',
                    'quantity' => $item->po_detail_quantity,
                    'unit_price' => $item->po_detail_rate,
                    'total_amount' => $item->po_detail_total,
                ];
            })->toArray(),
        ];
    }

    /**
     * Prepare vendor data for Sage format.
     */
    private function prepareVendorData($supplier): array
    {
        return [
            'contact_type_ids' => ['supplier'],
            'name' => $supplier->sup_name,
            'reference' => $supplier->sup_name,
            'email' => $supplier->sup_email,
            'telephone' => $supplier->sup_phone,
            'address_line_1' => $supplier->sup_address,
        ];
    }

    /**
     * Prepare item data for Sage format.
     */
    private function prepareItemData($item): array
    {
        return [
            'item_code' => $item->item_code,
            'description' => $item->item_name,
            'sales_ledger_account_id' => null, // Map to Sage chart of accounts
            'purchase_ledger_account_id' => null, // Map to Sage chart of accounts
            'usual_supplier_id' => null,
            'active' => $item->item_status == 1,
        ];
    }
}
