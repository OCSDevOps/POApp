@extends('layouts.admin')

@section('title', 'Cost Code Templates')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0 font-weight-bold text-primary">
                <i class="fas fa-layer-group me-1"></i> Cost Code Templates
            </h5>
            <small class="text-muted">Create reusable sets of cost codes for different project types</small>
        </div>
        <a href="{{ route('admin.costcode-templates.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> New Template
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if($templates->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-1"></i> No cost code templates found. Click "New Template" to create one.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover datatable" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Template Name</th>
                                <th>Description</th>
                                <th class="text-center">Cost Codes</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $template)
                                <tr>
                                    <td>{{ $template->cct_id }}</td>
                                    <td>
                                        <a href="{{ route('admin.costcode-templates.show', $template->cct_id) }}">
                                            <strong>{{ $template->cct_name }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ Str::limit($template->cct_description, 60) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $template->items_count }}</span>
                                    </td>
                                    <td>
                                        @if($template->cct_status)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $template->cct_createdate ? $template->cct_createdate->format('m/d/Y') : '-' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.costcode-templates.show', $template->cct_id) }}" class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.costcode-templates.edit', $template->cct_id) }}" class="btn btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.costcode-templates.destroy', $template->cct_id) }}"
                                                  class="d-inline" onsubmit="return confirm('Delete this template?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $templates->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
