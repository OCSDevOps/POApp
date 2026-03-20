@extends('layouts.admin')

@section('title', 'PO Template: ' . $template->pot_name)

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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-1 font-weight-bold text-primary">
                <i class="fas fa-file-alt me-1"></i> {{ $template->pot_name }}
                @if($template->pot_status === 'active')
                    <span class="badge bg-success ms-2">Active</span>
                @elseif($template->pot_status === 'inactive')
                    <span class="badge bg-secondary ms-2">Inactive</span>
                @else
                    <span class="badge bg-warning ms-2">{{ ucfirst($template->pot_status) }}</span>
                @endif
            </h5>
        </div>
        <div>
            <a href="{{ route('admin.template.createpo', $template->pot_id) }}" class="btn btn-success btn-sm">
                <i class="fas fa-shopping-cart me-1"></i> Create PO from Template
            </a>
            <a href="{{ route('admin.template.edit', $template->pot_id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <form action="{{ route('admin.template.duplicate', $template->pot_id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-success btn-sm" onclick="return confirm('Duplicate this template?')">
                    <i class="fas fa-copy me-1"></i> Duplicate
                </button>
            </form>
            <a href="{{ route('admin.template.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <form action="{{ route('admin.template.destroy', $template->pot_id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this template?')">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-1"></i> Template Details
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th style="width: 40%;">Name</th>
                            <td>{{ $template->pot_name }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $template->pot_description ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Default Project</th>
                            <td>{{ $template->defaultProject->proj_name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Default Supplier</th>
                            <td>{{ $template->defaultSupplier->sup_name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($template->pot_status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($template->pot_status === 'inactive')
                                    <span class="badge bg-secondary">Inactive</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($template->pot_status) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td>{{ optional($template->pot_created_at)->format('Y-m-d H:i') ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-1"></i> Template Items
                        <span class="badge bg-info ms-1">{{ $template->items->count() }}</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th class="text-end">Default Qty</th>
                                    <th>UOM</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($template->items as $templateItem)
                                    <tr>
                                        <td>{{ $templateItem->item->item_code ?? '—' }}</td>
                                        <td>{{ $templateItem->item->item_name ?? 'Item #' . $templateItem->poti_item_id }}</td>
                                        <td class="text-end">{{ $templateItem->poti_default_quantity ?? '—' }}</td>
                                        <td>{{ $templateItem->unitOfMeasure->uom_name ?? '—' }}</td>
                                        <td>{{ $templateItem->poti_notes ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No items in this template.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // No additional scripts needed for show view
});
</script>
@endpush
