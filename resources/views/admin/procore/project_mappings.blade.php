@extends('layouts.admin')

@section('title', 'Procore Project Mappings')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-project-diagram"></i> Project Mappings
        </h1>
        <a href="{{ route('admin.procore.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Procore Dashboard
        </a>
    </div>

    {{-- Mappings Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-link me-1"></i> Procore &harr; Local Project Mappings
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable" id="projectMappingsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Procore Project Name</th>
                            <th>Local Project</th>
                            <th>Last Synced</th>
                            <th style="width: 30%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mappings as $mapping)
                            <tr>
                                <td>
                                    <strong>{{ $mapping->procore_project_name }}</strong>
                                    <br><small class="text-muted">ID: {{ $mapping->procore_project_id }}</small>
                                </td>
                                <td>
                                    @if($mapping->local_project_id && $mapping->proj_name)
                                        <span class="badge bg-success me-1"><i class="fas fa-check"></i></span>
                                        {{ $mapping->proj_name }}
                                    @else
                                        <span class="badge bg-warning text-dark">Unmapped</span>
                                    @endif
                                </td>
                                <td>
                                    @if($mapping->last_synced_at)
                                        {{ \Carbon\Carbon::parse($mapping->last_synced_at)->format('M d, Y h:i A') }}
                                    @else
                                        <span class="text-muted">Never</span>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.procore.updateprojectmapping', $mapping->procore_project_id) }}" class="d-flex align-items-center gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="local_project_id" class="form-select form-select-sm">
                                            <option value="">-- Select Local Project --</option>
                                            @if($mapping->local_project_id && $mapping->proj_name)
                                                <option value="{{ $mapping->local_project_id }}" selected>
                                                    {{ $mapping->proj_name }} (current)
                                                </option>
                                            @endif
                                            @foreach($unmappedProjects as $project)
                                                <option value="{{ $project->proj_id }}">
                                                    {{ $project->proj_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No Procore projects found. Run a project sync first.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#projectMappingsTable').DataTable({
            pageLength: 25,
            responsive: true,
            order: [[0, 'asc']]
        });
    });
</script>
@endpush
