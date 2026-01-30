<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingIntegration;
use App\Models\IntegrationSyncLog;
use App\Services\Integrations\SageIntegrationService;
use App\Services\Integrations\QuickBooksIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class IntegrationController extends Controller
{
    /**
     * Display a listing of integrations.
     */
    public function index()
    {
        $integrations = AccountingIntegration::with(['company', 'syncLogs' => function ($query) {
            $query->latest()->limit(5);
        }])->get();

        return view('admin.integrations.index', compact('integrations'));
    }

    /**
     * Show the form for creating a new integration.
     */
    public function create()
    {
        return view('admin.integrations.create');
    }

    /**
     * Store a newly created integration.
     */
    public function store(Request $request)
    {
        $request->validate([
            'integration_type' => 'required|in:sage,quickbooks',
            'company_id' => 'required|exists:companies,id',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'settings' => 'nullable|json',
        ]);

        try {
            $integration = AccountingIntegration::create([
                'company_id' => $request->company_id,
                'integration_type' => $request->integration_type,
                'client_id' => encrypt($request->client_id),
                'client_secret' => encrypt($request->client_secret),
                'settings' => $request->settings ? json_decode($request->settings, true) : [],
                'is_active' => false, // Inactive until OAuth completed
            ]);

            // Redirect to OAuth authorization URL
            $authUrl = $this->getAuthorizationUrl($integration);

            return redirect($authUrl);
        } catch (\Exception $e) {
            Log::error('Integration creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to create integration: ' . $e->getMessage());
        }
    }

    /**
     * Handle OAuth callback from accounting system.
     */
    public function oauthCallback(Request $request, $integrationType)
    {
        $code = $request->query('code');
        $state = $request->query('state');

        if (!$code) {
            return redirect()->route('admin.integrations.index')
                ->with('error', 'OAuth authorization failed');
        }

        // Find integration by state (you should store state during creation)
        $integration = AccountingIntegration::where('integration_type', $integrationType)
            ->where('is_active', false)
            ->latest()
            ->first();

        if (!$integration) {
            return redirect()->route('admin.integrations.index')
                ->with('error', 'Integration not found');
        }

        try {
            // Exchange code for tokens
            $tokens = $this->exchangeCodeForTokens($integration, $code);

            $integration->update([
                'access_token' => encrypt($tokens['access_token']),
                'refresh_token' => encrypt($tokens['refresh_token']),
                'token_expires_at' => now()->addSeconds($tokens['expires_in']),
                'is_active' => true,
            ]);

            return redirect()->route('admin.integrations.index')
                ->with('success', 'Integration connected successfully');
        } catch (\Exception $e) {
            Log::error('OAuth callback failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('admin.integrations.index')
                ->with('error', 'Failed to complete OAuth: ' . $e->getMessage());
        }
    }

    /**
     * Test connection to accounting system.
     */
    public function testConnection($id)
    {
        $integration = AccountingIntegration::findOrFail($id);

        try {
            $service = $this->getService($integration);
            $success = $service->testConnection();

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Connection successful',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Connection failed',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Test connection failed', [
                'integration_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Manually trigger sync for purchase orders.
     */
    public function syncPurchaseOrders($id, Request $request)
    {
        $integration = AccountingIntegration::findOrFail($id);
        $poIds = $request->input('po_ids', []); // Specific POs or empty for all

        try {
            $service = $this->getService($integration);
            
            // Queue sync job or execute immediately
            $results = [];
            foreach ($poIds as $poId) {
                $po = \App\Models\PurchaseOrder::find($poId);
                if ($po) {
                    $result = $service->exportPurchaseOrder($po);
                    $results[] = $result;
                }
            }

            return response()->json([
                'success' => true,
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            Log::error('Sync purchase orders failed', [
                'integration_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Manually trigger sync for vendors.
     */
    public function syncVendors($id, Request $request)
    {
        $integration = AccountingIntegration::findOrFail($id);
        $direction = $request->input('direction', 'export'); // export or import

        try {
            $service = $this->getService($integration);
            
            if ($direction === 'import') {
                $result = $service->importVendors();
            } else {
                // Export all active vendors
                $vendors = \App\Models\Supplier::where('sup_status', 1)->get();
                $results = [];
                foreach ($vendors as $vendor) {
                    $results[] = $service->exportVendor($vendor);
                }
                $result = ['results' => $results];
            }

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('Sync vendors failed', [
                'integration_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View sync logs for an integration.
     */
    public function logs($id)
    {
        $integration = AccountingIntegration::findOrFail($id);
        $logs = $integration->syncLogs()
            ->latest()
            ->paginate(50);

        return view('admin.integrations.logs', compact('integration', 'logs'));
    }

    /**
     * Update integration settings.
     */
    public function update(Request $request, $id)
    {
        $integration = AccountingIntegration::findOrFail($id);

        $request->validate([
            'auto_sync_purchase_orders' => 'boolean',
            'auto_sync_vendors' => 'boolean',
            'auto_sync_items' => 'boolean',
            'settings' => 'nullable|json',
        ]);

        try {
            $integration->update([
                'auto_sync_purchase_orders' => $request->boolean('auto_sync_purchase_orders'),
                'auto_sync_vendors' => $request->boolean('auto_sync_vendors'),
                'auto_sync_items' => $request->boolean('auto_sync_items'),
                'settings' => $request->settings ? json_decode($request->settings, true) : $integration->settings,
            ]);

            return redirect()->route('admin.integrations.index')
                ->with('success', 'Integration updated successfully');
        } catch (\Exception $e) {
            Log::error('Integration update failed', [
                'integration_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to update integration: ' . $e->getMessage());
        }
    }

    /**
     * Toggle integration active status.
     */
    public function toggleActive($id)
    {
        $integration = AccountingIntegration::findOrFail($id);

        try {
            $integration->update([
                'is_active' => !$integration->is_active,
            ]);

            return response()->json([
                'success' => true,
                'is_active' => $integration->is_active,
            ]);
        } catch (\Exception $e) {
            Log::error('Toggle integration active failed', [
                'integration_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an integration.
     */
    public function destroy($id)
    {
        $integration = AccountingIntegration::findOrFail($id);

        try {
            DB::beginTransaction();

            // Delete related logs and mappings
            $integration->syncLogs()->delete();
            $integration->fieldMappings()->delete();
            $integration->delete();

            DB::commit();

            return redirect()->route('admin.integrations.index')
                ->with('success', 'Integration deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Integration deletion failed', [
                'integration_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to delete integration: ' . $e->getMessage());
        }
    }

    /**
     * Get the appropriate service instance for an integration.
     */
    private function getService(AccountingIntegration $integration)
    {
        return match ($integration->integration_type) {
            'sage' => new SageIntegrationService($integration),
            'quickbooks' => new QuickBooksIntegrationService($integration),
            default => throw new \Exception('Unknown integration type'),
        };
    }

    /**
     * Get OAuth authorization URL.
     */
    private function getAuthorizationUrl(AccountingIntegration $integration): string
    {
        $clientId = decrypt($integration->client_id);
        $redirectUri = route('admin.integrations.oauth.callback', $integration->integration_type);

        return match ($integration->integration_type) {
            'sage' => 'https://oauth.sage.com/oauth/authorize?' . http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'response_type' => 'code',
                'scope' => 'full_access',
            ]),
            'quickbooks' => 'https://appcenter.intuit.com/connect/oauth2?' . http_build_query([
                'client_id' => $clientId,
                'redirect_uri' => $redirectUri,
                'response_type' => 'code',
                'scope' => 'com.intuit.quickbooks.accounting',
            ]),
            default => throw new \Exception('Unknown integration type'),
        };
    }

    /**
     * Exchange authorization code for tokens.
     */
    private function exchangeCodeForTokens(AccountingIntegration $integration, string $code): array
    {
        // Implementation depends on integration type
        // This is a simplified version
        $service = $this->getService($integration);
        
        // Each service should implement token exchange
        // For now, return placeholder
        return [
            'access_token' => 'temp_token',
            'refresh_token' => 'temp_refresh',
            'expires_in' => 3600,
        ];
    }
}
