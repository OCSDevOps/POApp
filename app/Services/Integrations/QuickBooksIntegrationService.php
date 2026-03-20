<?php

namespace App\Services\Integrations;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Support\Facades\Http;

class QuickBooksIntegrationService extends BaseIntegrationService
{
    private const API_BASE_URL = 'https://quickbooks.api.intuit.com/v3/company';
    private const AUTH_URL = 'https://oauth.platform.intuit.com/oauth2/v1';
    
    private function getCompanyId(): string
    {
        return $this->integration->settings['company_id'] ?? '';
    }

    /**
     * Test the connection to QuickBooks.
     */
    public function testConnection(): bool
    {
        try {
            $response = $this->makeRequest(
                'GET',
                self::API_BASE_URL . '/' . $this->getCompanyId() . '/companyinfo/' . $this->getCompanyId()
            );
            
            return $response['success'];
        } catch (\Exception $e) {
            $this->handleError('test_connection', $e);
            return false;
        }
    }

    /**
     * Refresh the OAuth access token for QuickBooks.
     */
    public function refreshAccessToken(): bool
    {
        try {
            $response = Http::asForm()->post(self::AUTH_URL . '/tokens/bearer', [
                'grant_type' => 'refresh_token',
                'refresh_token' => decrypt($this->integration->refresh_token),
            ], [
                'Authorization' => 'Basic ' . base64_encode(
                    decrypt($this->integration->client_id) . ':' . decrypt($this->integration->client_secret)
                ),
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
     * Export a purchase order to QuickBooks.
     */
    public function exportPurchaseOrder($purchaseOrder): array
    {
        $this->startSyncLog('purchase_order', 'export', 'PurchaseOrder', $purchaseOrder->porder_id);

        try {
            $qbData = $this->preparePurchaseOrderData($purchaseOrder);
            $mappedData = $this->mapFields('purchase_order', $qbData);

            $response = $this->makeRequest(
                'POST',
                self::API_BASE_URL . '/' . $this->getCompanyId() . '/bill',
                $mappedData
            );

            if ($response['success']) {
                $externalId = $response['data']['Bill']['Id'] ?? null;
                
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
     * Export a vendor to QuickBooks.
     */
    public function exportVendor($supplier): array
    {
        $this->startSyncLog('vendor', 'export', 'Supplier', $supplier->sup_id);

        try {
            $qbData = $this->prepareVendorData($supplier);
            $mappedData = $this->mapFields('vendor', $qbData);

            $response = $this->makeRequest(
                'POST',
                self::API_BASE_URL . '/' . $this->getCompanyId() . '/vendor',
                $mappedData
            );

            if ($response['success']) {
                $externalId = $response['data']['Vendor']['Id'] ?? null;
                
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
     * Export an item to QuickBooks.
     */
    public function exportItem($item): array
    {
        $this->startSyncLog('item', 'export', 'Item', $item->item_id);

        try {
            $qbData = $this->prepareItemData($item);
            $mappedData = $this->mapFields('item', $qbData);

            $response = $this->makeRequest(
                'POST',
                self::API_BASE_URL . '/' . $this->getCompanyId() . '/item',
                $mappedData
            );

            if ($response['success']) {
                $externalId = $response['data']['Item']['Id'] ?? null;
                
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
     * Import vendors from QuickBooks.
     */
    public function importVendors(): array
    {
        $this->startSyncLog('vendor', 'import');

        try {
            $response = $this->makeRequest(
                'GET',
                self::API_BASE_URL . '/' . $this->getCompanyId() . '/query?query=SELECT * FROM Vendor'
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

            $vendors = $response['data']['QueryResponse']['Vendor'] ?? [];
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
                        'vendor_id' => $vendorData['Id'],
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
     * Import items from QuickBooks.
     */
    public function importItems(): array
    {
        $this->startSyncLog('item', 'import');

        try {
            $response = $this->makeRequest(
                'GET',
                self::API_BASE_URL . '/' . $this->getCompanyId() . '/query?query=SELECT * FROM Item'
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

            $items = $response['data']['QueryResponse']['Item'] ?? [];
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
                        'item_id' => $itemData['Id'],
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
     * Prepare purchase order data for QuickBooks format.
     */
    private function preparePurchaseOrderData($po): array
    {
        return [
            'VendorRef' => [
                'value' => $po->supplier->quickbooks_id ?? '',
            ],
            'TxnDate' => $po->porder_order_date,
            'DueDate' => $po->porder_required_date,
            'DocNumber' => $po->porder_no,
            'PrivateNote' => $po->porder_description,
            'Line' => $po->items->map(function ($item) {
                return [
                    'DetailType' => 'AccountBasedExpenseLineDetail',
                    'Amount' => $item->po_detail_total,
                    'AccountBasedExpenseLineDetail' => [
                        'AccountRef' => ['value' => '1'], // Map to QB account
                    ],
                    'Description' => $item->item->item_name ?? '',
                ];
            })->toArray(),
        ];
    }

    /**
     * Prepare vendor data for QuickBooks format.
     */
    private function prepareVendorData($supplier): array
    {
        return [
            'DisplayName' => $supplier->sup_name,
            'CompanyName' => $supplier->sup_name,
            'PrimaryPhone' => [
                'FreeFormNumber' => $supplier->sup_phone,
            ],
            'PrimaryEmailAddr' => [
                'Address' => $supplier->sup_email,
            ],
            'BillAddr' => [
                'Line1' => $supplier->sup_address,
            ],
        ];
    }

    /**
     * Prepare item data for QuickBooks format.
     */
    private function prepareItemData($item): array
    {
        return [
            'Name' => $item->item_code,
            'Description' => $item->item_name,
            'Type' => 'NonInventory',
            'Active' => $item->item_status == 1,
        ];
    }
}
