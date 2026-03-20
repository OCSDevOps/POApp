@extends('layouts.admin')

@section('title', 'Takeoffs & Estimates')

@section('content')
<div class="container-fluid">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-1"></i> Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.takeoffs.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-select">
                            <option value="">-- All Projects --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->proj_id }}" @selected(request('project_id') == $project->proj_id)>
                                    {{ $project->proj_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">-- All Statuses --</option>
                            @foreach(\App\Models\Takeoff::STATUS_LABELS as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') !== null && request('status') !== '' && request('status') == $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.takeoffs.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-times me-1"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Takeoffs Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-hard-hat me-1"></i> Takeoffs & Estimates
            </h6>
            <a href="{{ route('admin.takeoffs.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> New Takeoff
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="takeoffsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>TO Number</th>
                            <th>Project</th>
                            <th>Title</th>
                            <th class="text-center">Items</th>
                            <th class="text-end">Total ($)</th>
                            <th class="text-center">Status</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($takeoffs as $index => $takeoff)
                            <tr>
                                <td>{{ $takeoffs->firstItem() + $index }}</td>
                                <td>{{ $takeoff->to_number }}</td>
                                <td>{{ $takeoff->project->proj_name ?? '---' }}</td>
                                <td>{{ $takeoff->to_title }}</td>
                                <td class="text-center">{{ $takeoff->items_count ?? $takeoff->items->count() }}</td>
                                <td class="text-end">${{ number_format($takeoff->to_total ?? 0, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $takeoff->status_badge }}">{{ $takeoff->status_label }}</span>
                                </td>
                                <td>{{ optional($takeoff->created_at)->format('Y-m-d') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.takeoffs.show', $takeoff->to_id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(in_array($takeoff->to_status, [1, 3]))
                                        <a href="{{ route('admin.takeoffs.edit', $takeoff->to_id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger delete-btn" title="Delete"
                                            data-url="{{ route('admin.takeoffs.destroy', $takeoff->to_id) }}"
                                            data-name="{{ $takeoff->to_number }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No takeoffs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $takeoffs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</div>
@include('partials.delete-modal')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#takeoffsTable').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "order": [[0, 'asc']],
        "columnDefs": [
            { "orderable": false, "targets": 8 }
        ]
    });
});
</script>
@endpush
