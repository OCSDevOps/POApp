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
                ->where('project_id', $projectId)
                ->get()
                ->groupBy('role_name');

            $users = User::where('u_type', 1) // Active users only
                ->orderBy('name', 'ASC')
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
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:staff,project_manager,manager,director,finance,executive,admin',
            'can_approve' => 'boolean',
            'approval_limit' => 'nullable|numeric|min:0',
        ]);

        try {
            // Check if role assignment already exists
            $existing = ProjectRole::where('project_id', $request->project_id)
                ->where('user_id', $request->user_id)
                ->where('role_name', $request->role)
                ->first();

            if ($existing) {
                return back()->with('error', 'This role assignment already exists.');
            }

            ProjectRole::create([
                'company_id' => session('company_id'),
                'project_id' => $request->project_id,
                'user_id' => $request->user_id,
                'role_name' => $request->role,
                'can_approve_po' => $request->can_approve ?? false,
                'approval_limit' => $request->approval_limit,
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
                'can_approve_po' => $request->can_approve ?? false,
                'approval_limit' => $request->approval_limit,
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
            ->where('project_id', $projectId)
            ->where('role_name', $role)
            ->where('can_approve_po', true)
            ->get()
            ->map(function ($pr) {
                return [
                    'id' => $pr->user->id,
                    'name' => $pr->user->name,
                    'email' => $pr->user->email,
                    'approval_limit' => $pr->approval_limit,
                ];
            });

        return response()->json($users);
    }
}
