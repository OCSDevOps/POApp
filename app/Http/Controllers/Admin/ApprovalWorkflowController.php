<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalWorkflow;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalWorkflowController extends Controller
{
    /**
     * Display a listing of approval workflows.
     */
    public function index(Request $request)
    {
        $query = ApprovalWorkflow::query();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('aw_type', $request->type);
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('aw_project_id', $request->project_id);
        }

        $workflows = $query->orderBy('aw_type')->orderBy('aw_threshold_from')->get();

        $projects = Project::active()->orderByName()->get();

        return view('admin.approval-workflows.index', compact('workflows', 'projects'));
    }

    /**
     * Show the form for creating a new workflow.
     */
    public function create()
    {
        $projects = Project::active()->orderByName()->get();
        $users = User::where('u_type', 1)->orderBy('u_name', 'ASC')->get();

        return view('admin.approval-workflows.create', compact('projects', 'users'));
    }

    /**
     * Store a newly created workflow.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:budget_change_order,po_change_order,purchase_order',
            'project_id' => 'nullable|exists:project_master,proj_id',
            'threshold_from' => 'required|numeric|min:0',
            'threshold_to' => 'nullable|numeric|min:0',
            'approval_type' => 'required|in:role_based,user_based',
            
            // For user-based
            'approver_user_1' => 'required_if:approval_type,user_based|nullable|exists:user_master,u_id',
            'approver_user_2' => 'nullable|exists:user_master,u_id',
            'approver_user_3' => 'nullable|exists:user_master,u_id',
            
            // For role-based
            'approver_roles' => 'required_if:approval_type,role_based|nullable|array',
            'approver_roles.*' => 'in:staff,project_manager,manager,director,finance,executive,admin',
        ]);

        try {
            $approverRoles = null;
            $approverUser1 = null;
            $approverUser2 = null;
            $approverUser3 = null;

            if ($request->approval_type === 'role_based') {
                $approverRoles = json_encode($request->approver_roles);
            } else {
                $approverUser1 = $request->approver_user_1;
                $approverUser2 = $request->approver_user_2;
                $approverUser3 = $request->approver_user_3;
            }

            ApprovalWorkflow::create([
                'aw_type' => $request->type,
                'aw_project_id' => $request->project_id,
                'aw_threshold_from' => $request->threshold_from,
                'aw_threshold_to' => $request->threshold_to,
                'aw_approver_user_1' => $approverUser1,
                'aw_approver_user_2' => $approverUser2,
                'aw_approver_user_3' => $approverUser3,
                'aw_approver_roles' => $approverRoles,
                'aw_require_all' => $request->require_all ?? false,
                'aw_status' => 1,
                'aw_created_at' => now(),
                'aw_created_by' => auth()->id(),
            ]);

            return redirect()->route('admin.approval-workflows.index')
                ->with('success', 'Approval workflow created successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating workflow: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the workflow.
     */
    public function edit($id)
    {
        $workflow = ApprovalWorkflow::findOrFail($id);
        $projects = Project::active()->orderByName()->get();
        $users = User::where('u_type', 1)->orderBy('u_name', 'ASC')->get();

        return view('admin.approval-workflows.edit', compact('workflow', 'projects', 'users'));
    }

    /**
     * Update the workflow.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:budget_change_order,po_change_order,purchase_order',
            'project_id' => 'nullable|exists:project_master,proj_id',
            'threshold_from' => 'required|numeric|min:0',
            'threshold_to' => 'nullable|numeric|min:0',
            'approval_type' => 'required|in:role_based,user_based',
            'approver_user_1' => 'required_if:approval_type,user_based|nullable|exists:user_master,u_id',
            'approver_user_2' => 'nullable|exists:user_master,u_id',
            'approver_user_3' => 'nullable|exists:user_master,u_id',
            'approver_roles' => 'required_if:approval_type,role_based|nullable|array',
        ]);

        try {
            $workflow = ApprovalWorkflow::findOrFail($id);

            $approverRoles = null;
            $approverUser1 = null;
            $approverUser2 = null;
            $approverUser3 = null;

            if ($request->approval_type === 'role_based') {
                $approverRoles = json_encode($request->approver_roles);
            } else {
                $approverUser1 = $request->approver_user_1;
                $approverUser2 = $request->approver_user_2;
                $approverUser3 = $request->approver_user_3;
            }

            $workflow->update([
                'aw_type' => $request->type,
                'aw_project_id' => $request->project_id,
                'aw_threshold_from' => $request->threshold_from,
                'aw_threshold_to' => $request->threshold_to,
                'aw_approver_user_1' => $approverUser1,
                'aw_approver_user_2' => $approverUser2,
                'aw_approver_user_3' => $approverUser3,
                'aw_approver_roles' => $approverRoles,
                'aw_require_all' => $request->require_all ?? false,
                'aw_updated_at' => now(),
                'aw_updated_by' => auth()->id(),
            ]);

            return redirect()->route('admin.approval-workflows.index')
                ->with('success', 'Approval workflow updated successfully.');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating workflow: ' . $e->getMessage());
        }
    }

    /**
     * Toggle workflow status.
     */
    public function toggleStatus($id)
    {
        try {
            $workflow = ApprovalWorkflow::findOrFail($id);
            
            $workflow->update([
                'aw_status' => $workflow->aw_status ? 0 : 1,
                'aw_updated_at' => now(),
                'aw_updated_by' => auth()->id(),
            ]);

            $status = $workflow->aw_status ? 'activated' : 'deactivated';
            return back()->with('success', "Workflow {$status} successfully.");

        } catch (\Exception $e) {
            return back()->with('error', 'Error toggling status: ' . $e->getMessage());
        }
    }

    /**
     * Remove the workflow.
     */
    public function destroy($id)
    {
        try {
            $workflow = ApprovalWorkflow::findOrFail($id);
            $workflow->delete();

            return back()->with('success', 'Workflow deleted successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting workflow: ' . $e->getMessage());
        }
    }
}
