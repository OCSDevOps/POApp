<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectRoleController extends Controller
{
    /**
     * Display project role assignments.
     */
    public function index(Request $request)
    {
        $projectId = $request->get('project_id');
        
        $projects = Project::active()->orderByName()->get();
        
        if ($projectId) {
            $project = Project::findOrFail($projectId);
            $roles = ProjectRole::with('user')
                ->where('pr_project_id', $projectId)
                ->get()
                ->groupBy('pr_role');
            
            $users = User::where('u_type', 1) // Active users only
                ->orderBy('u_name', 'ASC')
                ->get();
            
            return view('admin.project-roles.index', compact('project', 'roles', 'users'));
        }
        
        return view('admin.project-roles.select', compact('projects'));
    }

    /**
     * Store a new role assignment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:project_master,proj_id',
            'user_id' => 'required|exists:user_master,u_id',
            'role' => 'required|in:staff,project_manager,manager,director,finance,executive,admin',
            'can_approve' => 'boolean',
            'approval_limit' => 'nullable|numeric|min:0',
        ]);

        try {
            // Check if role assignment already exists
            $existing = ProjectRole::where('pr_project_id', $request->project_id)
                ->where('pr_user_id', $request->user_id)
                ->where('pr_role', $request->role)
                ->first();

            if ($existing) {
                return back()->with('error', 'This role assignment already exists.');
            }

            ProjectRole::create([
                'pr_project_id' => $request->project_id,
                'pr_user_id' => $request->user_id,
                'pr_role' => $request->role,
                'pr_can_approve' => $request->can_approve ?? false,
                'pr_approval_limit' => $request->approval_limit,
                'pr_created_at' => now(),
                'pr_created_by' => auth()->id(),
            ]);

            return back()->with('success', 'Role assigned successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error assigning role: ' . $e->getMessage());
        }
    }

    /**
     * Update role assignment.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'can_approve' => 'boolean',
            'approval_limit' => 'nullable|numeric|min:0',
        ]);

        try {
            $role = ProjectRole::findOrFail($id);
            
            $role->update([
                'pr_can_approve' => $request->can_approve ?? false,
                'pr_approval_limit' => $request->approval_limit,
                'pr_updated_at' => now(),
                'pr_updated_by' => auth()->id(),
            ]);

            return back()->with('success', 'Role updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    /**
     * Remove role assignment.
     */
    public function destroy($id)
    {
        try {
            $role = ProjectRole::findOrFail($id);
            $role->delete();

            return back()->with('success', 'Role assignment removed successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error removing role: ' . $e->getMessage());
        }
    }

    /**
     * Get users by role for a project (AJAX).
     */
    public function getUsersByRole(Request $request)
    {
        $projectId = $request->get('project_id');
        $role = $request->get('role');

        $users = ProjectRole::with('user')
            ->where('pr_project_id', $projectId)
            ->where('pr_role', $role)
            ->where('pr_can_approve', true)
            ->get()
            ->map(function ($pr) {
                return [
                    'id' => $pr->user->u_id,
                    'name' => $pr->user->u_name,
                    'email' => $pr->user->u_email,
                    'approval_limit' => $pr->pr_approval_limit,
                ];
            });

        return response()->json($users);
    }
}
