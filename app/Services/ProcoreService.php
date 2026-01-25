<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Commitment;
use App\Models\CostCode;
use App\Models\ProcoreSyncLog;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcoreService
{
    protected $clientId;
    protected $secretKey;
    protected $companyId;
    protected $accessToken;
    protected $baseUrl = 'https://api.procore.com/rest/v1.0';

    public function __construct()
    {
        $this->loadProcoreAuth();
    }

    /**
     * Load Procore authentication details from database.
     */
    protected function loadProcoreAuth()
    {
        $auth = DB::table('procore_auth')->first();
        
        if ($auth) {
            $this->clientId = $auth->CLIENT_ID ?? null;
            $this->secretKey = $auth->SECRET_KEY ?? null;
            $this->companyId = $auth->COMPANY_ID ?? null;
        }
    }

    /**
     * Get Procore authentication details.
     */
    public function getProcoreAuth()
    {
        return [
            'CLIENT_ID' => $this->clientId,
            'SECRET_KEY' => $this->secretKey,
            'COMPANY_ID' => $this->companyId,
        ];
    }

    /**
     * Check if Procore is configured
     */
    public function isConfigured()
    {
        return !empty($this->clientId) && !empty($this->secretKey) && !empty($this->companyId);
    }

    /**
     * Get access token from Procore.
     */
    public function getAccessToken()
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            $response = Http::asForm()->post('https://login.procore.com/oauth/token', [
                'client_id' => $this->clientId,
                'client_secret' => $this->secretKey,
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'] ?? null;
                return $this->accessToken;
            }

            Log::error('Procore token request failed', ['response' => $response->body()]);
            return null;

        } catch (\Exception $e) {
            Log::error('Procore token request exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Make authenticated API request
     */
    protected function apiRequest($method, $endpoint, $params = [], $body = null)
    {
        $token = $this->getAccessToken();
        
        if (!$token) {
            throw new \Exception('Failed to get Procore access token');
        }

        $url = $this->baseUrl . $endpoint;
        
        $request = Http::withToken($token);
        
        if ($method === 'GET') {
            $response = $request->get($url, $params);
        } elseif ($method === 'POST') {
            $response = $request->post($url, array_merge($params, $body ?? []));
        } elseif ($method === 'PUT') {
            $response = $request->put($url, array_merge($params, $body ?? []));
        } elseif ($method === 'PATCH') {
            $response = $request->patch($url, array_merge($params, $body ?? []));
        } elseif ($method === 'DELETE') {
            $response = $request->delete($url, $params);
        }

        return $response;
    }

    // ==========================================
    // PROJECTS SYNC
    // ==========================================

    /**
     * Get projects from Procore.
     */
    public function getProjects()
    {
        try {
            $response = $this->apiRequest('GET', '/projects', [
                'company_id' => $this->companyId,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Procore projects request failed', ['response' => $response->body()]);
            return [];

        } catch (\Exception $e) {
            Log::error('Procore projects request exception', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Sync projects from Procore to local database
     */
    public function syncProjects()
    {
        $procoreProjects = $this->getProjects();
        $synced = 0;
        $errors = 0;

        foreach ($procoreProjects as $procoreProject) {
            try {
                $localProject = Project::where('procore_project_id', $procoreProject['id'])->first();

                $projectData = [
                    'proj_number' => $procoreProject['project_number'] ?? $procoreProject['id'],
                    'proj_name' => $procoreProject['name'],
                    'proj_address' => $this->formatAddress($procoreProject),
                    'proj_description' => $procoreProject['description'] ?? null,
                    'proj_status' => $procoreProject['active'] ? 1 : 0,
                    'procore_project_id' => $procoreProject['id'],
                    'proj_modifydate' => now(),
                ];

                if ($localProject) {
                    $localProject->update($projectData);
                } else {
                    $projectData['proj_createdate'] = now();
                    $projectData['proj_createby'] = auth()->id() ?? 1;
                    $localProject = Project::create($projectData);
                }

                ProcoreSyncLog::logSuccess(
                    ProcoreSyncLog::TYPE_PROJECTS,
                    ProcoreSyncLog::DIRECTION_INBOUND,
                    $localProject->proj_id,
                    $procoreProject['id'],
                    'Project synced successfully'
                );

                $synced++;

            } catch (\Exception $e) {
                ProcoreSyncLog::logFailure(
                    ProcoreSyncLog::TYPE_PROJECTS,
                    ProcoreSyncLog::DIRECTION_INBOUND,
                    null,
                    $procoreProject['id'] ?? null,
                    $e->getMessage()
                );
                $errors++;
            }
        }

        return ['synced' => $synced, 'errors' => $errors];
    }

    /**
     * Format address from Procore project data
     */
    protected function formatAddress($project)
    {
        $parts = array_filter([
            $project['address'] ?? null,
            $project['city'] ?? null,
            $project['state_code'] ?? null,
            $project['zip'] ?? null,
            $project['country_code'] ?? null,
        ]);
        return implode(', ', $parts);
    }

    // ==========================================
    // VENDORS/SUPPLIERS SYNC
    // ==========================================

    /**
     * Get vendors from Procore.
     */
    public function getVendors($projectId = null)
    {
        try {
            $params = ['company_id' => $this->companyId];
            
            $endpoint = $projectId 
                ? "/projects/{$projectId}/vendors"
                : "/vendors";

            $response = $this->apiRequest('GET', $endpoint, $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Procore vendors request failed', ['response' => $response->body()]);
            return [];

        } catch (\Exception $e) {
            Log::error('Procore vendors request exception', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Sync vendors from Procore to local suppliers
     */
    public function syncVendors()
    {
        $procoreVendors = $this->getVendors();
        $synced = 0;
        $errors = 0;

        foreach ($procoreVendors as $vendor) {
            try {
                $localSupplier = Supplier::where('procore_supplier_id', $vendor['id'])->first();

                $supplierData = [
                    'sup_name' => $vendor['name'],
                    'sup_address' => $this->formatVendorAddress($vendor),
                    'sup_contact_person' => $vendor['primary_contact']['name'] ?? '',
                    'sup_phone' => $vendor['phone'] ?? '',
                    'sup_email' => $vendor['email_address'] ?? '',
                    'sup_status' => $vendor['active'] ? 1 : 0,
                    'procore_supplier_id' => $vendor['id'],
                    'sup_modifydate' => now(),
                ];

                if ($localSupplier) {
                    $localSupplier->update($supplierData);
                } else {
                    $supplierData['sup_createdate'] = now();
                    $supplierData['sup_createby'] = auth()->id() ?? 1;
                    $localSupplier = Supplier::create($supplierData);
                }

                ProcoreSyncLog::logSuccess(
                    ProcoreSyncLog::TYPE_VENDORS,
                    ProcoreSyncLog::DIRECTION_INBOUND,
                    $localSupplier->sup_id,
                    $vendor['id'],
                    'Vendor synced successfully'
                );

                $synced++;

            } catch (\Exception $e) {
                ProcoreSyncLog::logFailure(
                    ProcoreSyncLog::TYPE_VENDORS,
                    ProcoreSyncLog::DIRECTION_INBOUND,
                    null,
                    $vendor['id'] ?? null,
                    $e->getMessage()
                );
                $errors++;
            }
        }

        return ['synced' => $synced, 'errors' => $errors];
    }

    /**
     * Format vendor address
     */
    protected function formatVendorAddress($vendor)
    {
        $parts = array_filter([
            $vendor['address'] ?? null,
            $vendor['city'] ?? null,
            $vendor['state_code'] ?? null,
            $vendor['zip'] ?? null,
        ]);
        return implode(', ', $parts);
    }

    // ==========================================
    // COST CODES SYNC
    // ==========================================

    /**
     * Get cost codes from Procore for a project
     */
    public function getCostCodes($projectId)
    {
        try {
            $response = $this->apiRequest('GET', "/cost_codes", [
                'project_id' => $projectId,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Procore cost codes request failed', ['response' => $response->body()]);
            return [];

        } catch (\Exception $e) {
            Log::error('Procore cost codes request exception', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Sync cost codes from Procore
     */
    public function syncCostCodes($procoreProjectId)
    {
        $costCodes = $this->getCostCodes($procoreProjectId);
        $synced = 0;
        $errors = 0;

        foreach ($costCodes as $cc) {
            try {
                $localCostCode = CostCode::where('procore_cost_code_id', $cc['id'])->first();

                $ccData = [
                    'cc_no' => $cc['full_code'] ?? $cc['code'],
                    'cc_description' => $cc['name'],
                    'cc_details' => $cc['long_name_with_code'] ?? null,
                    'cc_status' => 1,
                    'procore_cost_code_id' => $cc['id'],
                    'cc_modifydate' => now(),
                ];

                if ($localCostCode) {
                    $localCostCode->update($ccData);
                } else {
                    $ccData['cc_createdate'] = now();
                    $ccData['cc_createby'] = auth()->id() ?? 1;
                    $localCostCode = CostCode::create($ccData);
                }

                // Store mapping
                DB::table('procore_cost_code_mapping')->updateOrInsert(
                    ['pccm_local_cost_code_id' => $localCostCode->cc_id],
                    [
                        'pccm_procore_cost_code_id' => $cc['id'],
                        'pccm_procore_project_id' => $procoreProjectId,
                        'pccm_last_sync_at' => now(),
                    ]
                );

                ProcoreSyncLog::logSuccess(
                    ProcoreSyncLog::TYPE_COST_CODES,
                    ProcoreSyncLog::DIRECTION_INBOUND,
                    $localCostCode->cc_id,
                    $cc['id'],
                    'Cost code synced successfully'
                );

                $synced++;

            } catch (\Exception $e) {
                ProcoreSyncLog::logFailure(
                    ProcoreSyncLog::TYPE_COST_CODES,
                    ProcoreSyncLog::DIRECTION_INBOUND,
                    null,
                    $cc['id'] ?? null,
                    $e->getMessage()
                );
                $errors++;
            }
        }

        return ['synced' => $synced, 'errors' => $errors];
    }

    // ==========================================
    // BUDGETS SYNC
    // ==========================================

    /**
     * Get budget detail rows from Procore.
     */
    public function getBudgetDetailRows($projectId, $budgetViewId = null)
    {
        try {
            $params = [
                'company_id' => $this->companyId,
                'project_id' => $projectId,
            ];

            $endpoint = $budgetViewId 
                ? "/budget_views/{$budgetViewId}/detail_rows"
                : "/budget_line_items";

            $response = $this->apiRequest('GET', $endpoint, $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Procore budget detail rows request failed', ['response' => $response->body()]);
            return [];

        } catch (\Exception $e) {
            Log::error('Procore budget detail rows request exception', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Sync budgets from Procore
     */
    public function syncBudgets($procoreProjectId)
    {
        $budgetRows = $this->getBudgetDetailRows($procoreProjectId);
        $synced = 0;
        $errors = 0;

        // Get local project
        $localProject = Project::where('procore_project_id', $procoreProjectId)->first();
        if (!$localProject) {
            return ['synced' => 0, 'errors' => 1, 'message' => 'Local project not found'];
        }

        foreach ($budgetRows as $bli) {
            try {
                // Find or create cost code
                $costCodeMapping = DB::table('procore_cost_code_mapping')
                    ->where('pccm_procore_cost_code_id', $bli['cost_code_id'] ?? 0)
                    ->first();

                if (!$costCodeMapping) {
                    continue; // Skip if cost code not mapped
                }

                $localBudget = Budget::where('procore_budget_id', $bli['id'])->first();

                $budgetData = [
                    'budget_project_id' => $localProject->proj_id,
                    'budget_cost_code_id' => $costCodeMapping->pccm_local_cost_code_id,
                    'budget_original_amount' => $bli['original_budget_amount'] ?? 0,
                    'budget_revised_amount' => $bli['revised_budget'] ?? $bli['original_budget_amount'] ?? 0,
                    'budget_committed_amount' => $bli['committed_costs'] ?? 0,
                    'budget_spent_amount' => $bli['direct_costs'] ?? 0,
                    'budget_fiscal_year' => date('Y'),
                    'budget_status' => 1,
                    'procore_budget_id' => $bli['id'],
                    'budget_modified_at' => now(),
                ];

                if ($localBudget) {
                    $localBudget->update($budgetData);
                } else {
                    $budgetData['budget_created_at'] = now();
                    $budgetData['budget_created_by'] = auth()->id() ?? 1;
                    $localBudget = Budget::create($budgetData);
                }

                ProcoreSyncLog::logSuccess(
                    ProcoreSyncLog::TYPE_BUDGETS,
                    ProcoreSyncLog::DIRECTION_INBOUND,
                    $localBudget->budget_id,
                    $bli['id'],
                    'Budget synced successfully'
                );

                $synced++;

            } catch (\Exception $e) {
                ProcoreSyncLog::logFailure(
                    ProcoreSyncLog::TYPE_BUDGETS,
                    ProcoreSyncLog::DIRECTION_INBOUND,
                    null,
                    $bli['id'] ?? null,
                    $e->getMessage()
                );
                $errors++;
            }
        }

        return ['synced' => $synced, 'errors' => $errors];
    }

    // ==========================================
    // COMMITMENTS SYNC
    // ==========================================

    /**
     * Get commitments (purchase order contracts) from Procore
     */
    public function getCommitments($projectId)
    {
        try {
            $response = $this->apiRequest('GET', "/purchase_order_contracts", [
                'project_id' => $projectId,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Procore commitments request failed', ['response' => $response->body()]);
            return [];

        } catch (\Exception $e) {
            Log::error('Procore commitments request exception', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Sync commitments from Procore
     */
    public function syncCommitments($procoreProjectId)
    {
        $commitments = $this->getCommitments($procoreProjectId);
        $synced = 0;
        $errors = 0;

        // Get local project
        $localProject = Project::where('procore_project_id', $procoreProjectId)->first();
        if (!$localProject) {
            return ['synced' => 0, 'errors' => 1, 'message' => 'Local project not found'];
        }

        foreach ($commitments as $commit) {
            try {
                // Find supplier
                $localSupplier = Supplier::where('procore_supplier_id', $commit['vendor_id'] ?? 0)->first();
                
                // Find cost code (use first line item's cost code if available)
                $costCodeId = null;
                if (!empty($commit['line_items'][0]['cost_code_id'])) {
                    $ccMapping = DB::table('procore_cost_code_mapping')
                        ->where('pccm_procore_cost_code_id', $commit['line_items'][0]['cost_code_id'])
                        ->first();
                    $costCodeId = $ccMapping ? $ccMapping->pccm_local_cost_code_id : null;
                }

                // Default cost code if not found
                if (!$costCodeId) {
                    $defaultCostCode = CostCode::first();
                    $costCodeId = $defaultCostCode ? $defaultCostCode->cc_id : 1;
                }

                $localCommitment = Commitment::where('procore_commitment_id', $commit['id'])->first();

                $statusMap = [
                    'Draft' => Commitment::STATUS_DRAFT,
                    'Pending' => Commitment::STATUS_PENDING,
                    'Approved' => Commitment::STATUS_APPROVED,
                    'Active' => Commitment::STATUS_ACTIVE,
                    'Complete' => Commitment::STATUS_COMPLETED,
                    'Void' => Commitment::STATUS_CANCELLED,
                ];

                $commitData = [
                    'commit_project_id' => $localProject->proj_id,
                    'commit_supplier_id' => $localSupplier ? $localSupplier->sup_id : 1,
                    'commit_cost_code_id' => $costCodeId,
                    'commit_number' => $commit['number'] ?? $commit['id'],
                    'commit_title' => $commit['title'] ?? 'Commitment ' . $commit['id'],
                    'commit_description' => $commit['description'] ?? null,
                    'commit_original_value' => $commit['grand_total'] ?? 0,
                    'commit_approved_cos' => $commit['approved_change_orders'] ?? 0,
                    'commit_pending_cos' => $commit['pending_change_orders'] ?? 0,
                    'commit_invoiced_amount' => $commit['invoiced_amount'] ?? 0,
                    'commit_paid_amount' => $commit['paid_amount'] ?? 0,
                    'commit_start_date' => $commit['start_date'] ?? null,
                    'commit_end_date' => $commit['completion_date'] ?? null,
                    'commit_status' => $statusMap[$commit['status']] ?? Commitment::STATUS_DRAFT,
                    'procore_commitment_id' => $commit['id'],
                    'commit_modified_at' => now(),
                ];

                if ($localCommitment) {
                    $localCommitment->update($commitData);
                } else {
                    $commitData['commit_created_at'] = now();
                    $commitData['commit_created_by'] = auth()->id() ?? 1;
                    $localCommitment = Commitment::create($commitData);
                }

                ProcoreSyncLog::logSuccess(
                    ProcoreSyncLog::TYPE_COMMITMENTS,
                    ProcoreSyncLog::DIRECTION_INBOUND,
                    $localCommitment->commit_id,
                    $commit['id'],
                    'Commitment synced successfully'
                );

                $synced++;

            } catch (\Exception $e) {
                ProcoreSyncLog::logFailure(
                    ProcoreSyncLog::TYPE_COMMITMENTS,
                    ProcoreSyncLog::DIRECTION_INBOUND,
                    null,
                    $commit['id'] ?? null,
                    $e->getMessage()
                );
                $errors++;
            }
        }

        return ['synced' => $synced, 'errors' => $errors];
    }

    // ==========================================
    // PURCHASE ORDERS SYNC (OUTBOUND)
    // ==========================================

    /**
     * Create purchase order in Procore.
     */
    public function createPurchaseOrder($purchaseOrder)
    {
        try {
            $project = Project::find($purchaseOrder->porder_project_ms);

            if (!$project || !$project->procore_project_id) {
                return ['success' => false, 'error' => 'Project not linked to Procore'];
            }

            $supplier = Supplier::find($purchaseOrder->porder_supplier_ms);

            $payload = [
                'purchase_order_contract' => [
                    'title' => $purchaseOrder->porder_no,
                    'status' => 'Draft',
                    'vendor_id' => $supplier->procore_supplier_id ?? null,
                    'grand_total' => $purchaseOrder->porder_total_amount,
                    'description' => $purchaseOrder->porder_description,
                ]
            ];

            $response = $this->apiRequest('POST', '/purchase_order_contracts', [
                'company_id' => $this->companyId,
                'project_id' => $project->procore_project_id,
            ], $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Update local PO with Procore ID
                $purchaseOrder->update([
                    'procore_po_id' => $data['id'] ?? null,
                    'integration_status' => 'synced',
                ]);

                ProcoreSyncLog::logSuccess(
                    ProcoreSyncLog::TYPE_PURCHASE_ORDERS,
                    ProcoreSyncLog::DIRECTION_OUTBOUND,
                    $purchaseOrder->porder_id,
                    $data['id'],
                    'Purchase order created in Procore',
                    $payload,
                    $data
                );

                return ['success' => true, 'data' => $data];
            }

            ProcoreSyncLog::logFailure(
                ProcoreSyncLog::TYPE_PURCHASE_ORDERS,
                ProcoreSyncLog::DIRECTION_OUTBOUND,
                $purchaseOrder->porder_id,
                null,
                $response->body(),
                $payload,
                $response->json()
            );

            return ['success' => false, 'error' => $response->body()];

        } catch (\Exception $e) {
            ProcoreSyncLog::logFailure(
                ProcoreSyncLog::TYPE_PURCHASE_ORDERS,
                ProcoreSyncLog::DIRECTION_OUTBOUND,
                $purchaseOrder->porder_id ?? null,
                null,
                $e->getMessage()
            );

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    // ==========================================
    // FULL SYNC
    // ==========================================

    /**
     * Perform full sync for a project
     */
    public function fullSync($procoreProjectId)
    {
        $results = [
            'projects' => $this->syncProjects(),
            'vendors' => $this->syncVendors(),
            'cost_codes' => $this->syncCostCodes($procoreProjectId),
            'budgets' => $this->syncBudgets($procoreProjectId),
            'commitments' => $this->syncCommitments($procoreProjectId),
        ];

        return $results;
    }

    /**
     * Get sync statistics
     */
    public function getSyncStatistics($days = 30)
    {
        return ProcoreSyncLog::getStatistics($days);
    }
}
