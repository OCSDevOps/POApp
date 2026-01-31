<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Project;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index()
    {
        $projects = Project::orderBy('proj_id', 'DESC')->get();
        
        return view('admin.project.project_list_view', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('admin.project.add_project');
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $request->validate([
            'proj_name' => 'required|string|max:255',
            'proj_code' => 'required|string|max:50|unique:project_master,proj_code',
        ]);

        Project::create([
            'proj_name' => $request->proj_name,
            'proj_code' => $request->proj_code,
            'proj_address' => $request->proj_address,
            'proj_city' => $request->proj_city,
            'proj_state' => $request->proj_state,
            'proj_zip' => $request->proj_zip,
            'proj_country' => $request->proj_country,
            'proj_start_date' => $request->proj_start_date,
            'proj_end_date' => $request->proj_end_date,
            'proj_status' => 1,
            'proj_created_by' => Auth::id(),
            'proj_created_at' => now(),
            'company_id' => session('company_id'),
        ]);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }

    /**
     * Display the specified project.
     */
    public function show($id)
    {
        $project = Project::with('details')->findOrFail($id);
        
        abort_unless($project->company_id === session('company_id'), 403);
        
        return view('admin.project.view_project', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit($id)
    {
        $project = Project::findOrFail($id);
        
        abort_unless($project->company_id === session('company_id'), 403);
        
        return view('admin.project.edit_project', compact('project'));
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'proj_name' => 'required|string|max:255',
            'proj_code' => 'required|string|max:50|unique:project_master,proj_code,' . $id . ',proj_id',
        ]);

        $project = Project::findOrFail($id);
        
        abort_unless($project->company_id === session('company_id'), 403);

        $project->update([
            'proj_name' => $request->proj_name,
            'proj_code' => $request->proj_code,
            'proj_address' => $request->proj_address,
            'proj_city' => $request->proj_city,
            'proj_state' => $request->proj_state,
            'proj_zip' => $request->proj_zip,
            'proj_country' => $request->proj_country,
            'proj_start_date' => $request->proj_start_date,
            'proj_end_date' => $request->proj_end_date,
            'proj_modified_by' => Auth::id(),
            'proj_modified_at' => now(),
        ]);

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    /**
     * Update project status.
     */
    public function updateStatus(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        
        $project->update([
            'proj_status' => $request->status,
            'proj_modified_by' => Auth::id(),
            'proj_modified_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    /**
     * Remove the specified project.
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        
        abort_unless($project->company_id === session('company_id'), 403);
        
        // Check if project has purchase orders
        $hasPO = DB::table('purchase_order_master')
            ->where('porder_project_ms', $id)
            ->where('company_id', session('company_id'))
            ->exists();

        if ($hasPO) {
            return back()->with('error', 'Cannot delete project with existing purchase orders.');
        }

        $project->delete();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project deleted successfully.');
    }
}
