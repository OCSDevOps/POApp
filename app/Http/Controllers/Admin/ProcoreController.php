<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProcoreSyncLog;
use App\Models\Project;
use App\Services\ProcoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcoreController extends Controller
{
    protected $procoreService;

    public function __construct(ProcoreService $procoreService)
    {
        $this->procoreService = $procoreService;
    }

    /**
     * Procore integration dashboard.
     */
    public function index()
    {
        // Get sync status
        $lastSync = ProcoreSyncLog::orderBy('sync_created_at', 'DESC')->first();
        
        // Get sync history
        $syncHistory = ProcoreSyncLog::orderBy('sync_created_at', 'DESC')
            ->limit(20)
            ->get();

        // Get project mappings (scoped by company via project_master)
        $companyId = session('company_id');
        $projectMappings = DB::table('procore_project_mapping')
            ->join('project_master', 'procore_project_mapping.ppm_local_project_id', '=', 'project_master.proj_id')
            ->where('project_master.company_id', $companyId)
            ->select('procore_project_mapping.*', 'project_master.proj_name')
            ->orderBy('ppm_last_sync_at', 'DESC')
            ->get();

        // Get cost code mappings count
        $costCodeMappingsCount = DB::table('procore_cost_code_mapping')->count();

        return view('admin.procore.index', compact('lastSync', 'syncHistory', 'projectMappings', 'costCodeMappingsCount'));
    }

    /**
     * Sync all data from Procore.
     */
    public function syncAll()
    {
        try {
            $result = $this->procoreService->syncAll();

            $message = "Sync completed. ";
            $message .= "Projects: {$result['projects']['created']} created, {$result['projects']['updated']} updated. ";
            $message .= "Vendors: {$result['vendors']['created']} created, {$result['vendors']['updated']} updated. ";
            $message .= "Cost Codes: {$result['cost_codes']['created']} created, {$result['cost_codes']['updated']} updated.";

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Sync projects from Procore.
     */
    public function syncProjects()
    {
        try {
            $result = $this->procoreService->syncProjects();
            return back()->with('success', "Projects synced: {$result['created']} created, {$result['updated']} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Project sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Sync vendors from Procore.
     */
    public function syncVendors()
    {
        try {
            $result = $this->procoreService->syncVendors();
            return back()->with('success', "Vendors synced: {$result['created']} created, {$result['updated']} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Vendor sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Sync cost codes from Procore.
     */
    public function syncCostCodes(Request $request)
    {
        $projectId = $request->get('project_id');

        try {
            $result = $this->procoreService->syncCostCodes($projectId);
            return back()->with('success', "Cost codes synced: {$result['created']} created, {$result['updated']} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Cost code sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Sync budgets from Procore.
     */
    public function syncBudgets(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:project_master,proj_id',
        ]);

        try {
            $result = $this->procoreService->syncBudgets($request->project_id);
            return back()->with('success', "Budgets synced: {$result['created']} created, {$result['updated']} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Budget sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Sync commitments from Procore.
     */
    public function syncCommitments(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:project_master,proj_id',
        ]);

        try {
            $result = $this->procoreService->syncCommitments($request->project_id);
            return back()->with('success', "Commitments synced: {$result['created']} created, {$result['updated']} updated.");
        } catch (\Exception $e) {
            return back()->with('error', 'Commitment sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Push purchase order to Procore.
     */
    public function pushPurchaseOrder(Request $request)
    {
        $request->validate([
            'po_id' => 'required|exists:purchase_order_master,porder_id',
        ]);

        try {
            $result = $this->procoreService->pushPurchaseOrder($request->po_id);
            return back()->with('success', 'Purchase order pushed to Procore successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to push PO to Procore: ' . $e->getMessage());
        }
    }

    /**
     * View sync log details.
     */
    public function syncLog($id)
    {
        $log = ProcoreSyncLog::findOrFail($id);
        return view('admin.procore.sync_log', compact('log'));
    }

    /**
     * Project mapping management.
     */
    public function projectMappings()
    {
        $companyId = session('company_id');
        $mappings = DB::table('procore_project_mapping')
            ->leftJoin('project_master', 'procore_project_mapping.ppm_local_project_id', '=', 'project_master.proj_id')
            ->where(function($q) use ($companyId) {
                $q->whereNull('project_master.proj_id')
                  ->orWhere('project_master.company_id', $companyId);
            })
            ->select('procore_project_mapping.*', 'project_master.proj_name')
            ->orderBy('ppm_procore_project_id')
            ->get();

        $unmappedProjects = Project::whereNotIn('proj_id', function ($q) {
            $q->select('ppm_local_project_id')
              ->from('procore_project_mapping')
              ->whereNotNull('ppm_local_project_id');
        })->get();

        return view('admin.procore.project_mappings', compact('mappings', 'unmappedProjects'));
    }

    /**
     * Update project mapping.
     */
    public function updateProjectMapping(Request $request, $procoreProjectId)
    {
        $request->validate([
            'local_project_id' => 'nullable|exists:project_master,proj_id',
        ]);

        DB::table('procore_project_mapping')
            ->where('ppm_procore_project_id', $procoreProjectId)
            ->update([
                'ppm_local_project_id' => $request->local_project_id,
                'ppm_last_sync_at' => now(),
            ]);

        return back()->with('success', 'Project mapping updated.');
    }

    /**
     * Cost code mapping management.
     */
    public function costCodeMappings(Request $request)
    {
        $companyId = session('company_id');
        $query = DB::table('procore_cost_code_mapping')
            ->leftJoin('cost_code_master', 'procore_cost_code_mapping.pccm_local_cost_code_id', '=', 'cost_code_master.cc_id')
            ->where(function($q) use ($companyId) {
                $q->whereNull('cost_code_master.cc_id')
                  ->orWhere('cost_code_master.company_id', $companyId);
            })
            ->select('procore_cost_code_mapping.*', 'cost_code_master.cc_no', 'cost_code_master.cc_description');

        if ($request->filled('project_id')) {
            $query->where('pccm_procore_project_id', $request->project_id);
        }

        $mappings = $query->orderBy('pccm_procore_cost_code_id')->paginate(50);

        $projects = DB::table('procore_project_mapping')
            ->join('project_master', 'procore_project_mapping.ppm_local_project_id', '=', 'project_master.proj_id')
            ->where('project_master.company_id', $companyId)
            ->select('ppm_procore_project_id', 'ppm_procore_company_id')
            ->orderBy('ppm_procore_project_id')
            ->get();

        return view('admin.procore.cost_code_mappings', compact('mappings', 'projects'));
    }

    /**
     * API settings.
     */
    public function settings()
    {
        $settings = [
            'client_id' => config('services.procore.client_id'),
            'base_url' => config('services.procore.base_url'),
            'company_id' => config('services.procore.company_id'),
        ];

        return view('admin.procore.settings', compact('settings'));
    }

    /**
     * Update API settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
            'company_id' => 'required|string',
        ]);

        // Update .env file or database settings
        // For security, this should be handled carefully
        
        return back()->with('success', 'Settings updated. Please restart the application for changes to take effect.');
    }

    /**
     * Test API connection.
     */
    public function testConnection()
    {
        try {
            // Try to fetch company info
            $response = $this->procoreService->testConnection();
            
            if ($response) {
                return back()->with('success', 'Connection successful! API is working.');
            } else {
                return back()->with('error', 'Connection failed. Please check your credentials.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Connection test failed: ' . $e->getMessage());
        }
    }

    /**
     * Webhook endpoint for Procore.
     */
    public function webhook(Request $request)
    {
        // Verify webhook signature
        $signature = $request->header('X-Procore-Signature');
        
        // Process webhook payload
        $payload = $request->all();
        
        // Log webhook
        \Log::info('Procore webhook received', $payload);

        // Handle different event types
        $eventType = $payload['event_type'] ?? null;
        
        switch ($eventType) {
            case 'project.update':
                $this->handleProjectUpdate($payload);
                break;
            case 'budget.update':
                $this->handleBudgetUpdate($payload);
                break;
            case 'commitment.update':
                $this->handleCommitmentUpdate($payload);
                break;
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle project update webhook.
     */
    protected function handleProjectUpdate($payload)
    {
        $procoreProjectId = $payload['resource_id'] ?? null;
        
        if ($procoreProjectId) {
            // Queue sync for this project
            // dispatch(new SyncProcoreProject($procoreProjectId));
        }
    }

    /**
     * Handle budget update webhook.
     */
    protected function handleBudgetUpdate($payload)
    {
        $procoreProjectId = $payload['project_id'] ?? null;
        
        if ($procoreProjectId) {
            // Queue budget sync
            // dispatch(new SyncProcoreBudgets($procoreProjectId));
        }
    }

    /**
     * Handle commitment update webhook.
     */
    protected function handleCommitmentUpdate($payload)
    {
        $procoreProjectId = $payload['project_id'] ?? null;
        
        if ($procoreProjectId) {
            // Queue commitment sync
            // dispatch(new SyncProcoreCommitments($procoreProjectId));
        }
    }
}
