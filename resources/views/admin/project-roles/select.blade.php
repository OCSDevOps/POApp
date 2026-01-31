@extends('layouts.admin')

@section('title', 'Project Role Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3"><i class="fas fa-users-cog"></i> Project Role Management</h1>
            <p class="text-muted">Select a project to manage role assignments and approval permissions</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Select Project</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.project-roles.index') }}" method="GET">
                        <div class="mb-3">
                            <label for="project_id" class="form-label">Project</label>
                            <select name="project_id" id="project_id" class="form-select" required>
                                <option value="">Select a project...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->proj_id }}">
                                        {{ $project->proj_name }} ({{ $project->proj_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i> Continue
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
