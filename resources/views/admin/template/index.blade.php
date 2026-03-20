@extends('layouts.admin')

@section('title', 'PO Templates')

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

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-file-alt me-1"></i> PO Templates
            </h6>
            <a href="{{ route('admin.template.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Create Template
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="templatesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Default Project</th>
                            <th>Default Supplier</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>{{ $template->pot_name }}</td>
                                <td>{{ Str::limit($template->pot_description, 60) }}</td>
                                <td>{{ $template->defaultProject->proj_name ?? '—' }}</td>
                                <td>{{ $template->defaultSupplier->sup_name ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $template->items->count() }}</span>
                                </td>
                                <td>
                                    @if($template->pot_status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($template->pot_status === 'inactive')
                                        <span class="badge bg-secondary">Inactive</span>
                                    @else
                                        <span class="badge bg-warning">{{ ucfirst($template->pot_status) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.template.show', $template->pot_id) }}" class="btn btn-sm btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.template.edit', $template->pot_id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.template.duplicate', $template->pot_id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Duplicate" onclick="return confirm('Duplicate this template?')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" title="Delete"
                                            data-url="{{ route('admin.template.destroy', $template->pot_id) }}" data-name="{{ $template->pot_name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No templates found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $templates->links() }}
            </div>
        </div>
    </div>

</div>
@include('partials.delete-modal')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#templatesTable').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "order": [[0, 'asc']],
        "columnDefs": [
            { "orderable": false, "targets": 6 }
        ]
    });
});
</script>
@endpush
