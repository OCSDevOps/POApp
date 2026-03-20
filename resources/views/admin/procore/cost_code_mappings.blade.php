@extends('layouts.admin')

@section('title', 'Procore Cost Code Mappings')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-code"></i> Cost Code Mappings
        </h1>
        <a href="{{ route('admin.procore.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Procore Dashboard
        </a>
    </div>

    {{-- Filter Card --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.procore.costcodemappings') }}">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <label for="procore_project" class="form-label fw-bold">Filter by Procore Project</label>
                        <select name="procore_project_id" id="procore_project" class="form-select">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->procore_project_id }}" {{ request('procore_project_id') == $project->procore_project_id ? 'selected' : '' }}>
                                    {{ $project->procore_project_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.procore.costcodemappings') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Mappings Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-link me-1"></i> Procore &harr; Local Cost Code Mappings
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Procore Cost Code</th>
                            <th>Local Cost Code</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mappings as $mapping)
                            <tr>
                                <td>
                                    <strong>{{ $mapping->procore_cost_code }}</strong>
                                </td>
                                <td>
                                    @if($mapping->local_cost_code_id && $mapping->cc_no)
                                        {{ $mapping->cc_no }} - {{ $mapping->cc_description }}
                                    @else
                                        <span class="text-muted fst-italic">Unmapped</span>
                                    @endif
                                </td>
                                <td>
                                    @if($mapping->local_cost_code_id)
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Mapped</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-circle me-1"></i> Unmapped</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    No cost code mappings found. Run a cost code sync first.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($mappings->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $mappings->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // No additional scripts needed — server-side pagination used
</script>
@endpush
