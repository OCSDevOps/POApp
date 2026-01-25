@extends('layouts.admin')

@section('title', 'Purchase Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Purchase Orders</h1>
    <a href="{{ route('admin.porder.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> New Purchase Order
    </a>
</div>

<!-- Filters Card -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter me-1"></i> Filters
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.porder.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="project" class="form-label">Project</label>
                <select name="project" id="project" class="form-select">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->proj_id }}" {{ ($filters['project'] ?? '') == $project->proj_id ? 'selected' : '' }}>
                            {{ $project->proj_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="supplier" class="form-label">Supplier</label>
                <select name="supplier" id="supplier" class="form-select">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->sup_id }}" {{ ($filters['supplier'] ?? '') == $supplier->sup_id ? 'selected' : '' }}>
                            {{ $supplier->sup_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ ($filters['status'] ?? '') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="submitted" {{ ($filters['status'] ?? '') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="approved" {{ ($filters['status'] ?? '') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ ($filters['status'] ?? '') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
                <a href="{{ route('admin.porder.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Clear
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Purchase Orders Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-table me-1"></i> Purchase Orders List
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover datatable" id="poTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>PO Number</th>
                        <th>Project</th>
                        <th>Supplier</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Delivery</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrders as $index => $po)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('admin.porder.show', $po->porder_id) }}">
                                    {{ $po->porder_no }}
                                </a>
                            </td>
                            <td>{{ $po->project->proj_name ?? 'N/A' }}</td>
                            <td>{{ $po->supplier->sup_name ?? 'N/A' }}</td>
                            <td>{{ $po->porder_type }}</td>
                            <td>{{ $po->porder_date ? date('M d, Y', strtotime($po->porder_date)) : 'N/A' }}</td>
                            <td class="text-end">${{ number_format($po->porder_grand_total ?? 0, 2) }}</td>
                            <td>
                                @php
                                    $statusClass = match($po->porder_general_status) {
                                        'pending' => 'warning',
                                        'submitted' => 'info',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ ucfirst($po->porder_general_status ?? 'Unknown') }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $deliveryClass = match($po->porder_delivery_status) {
                                        '0' => 'danger',
                                        '1' => 'success',
                                        '2' => 'warning',
                                        default => 'secondary'
                                    };
                                    $deliveryText = match($po->porder_delivery_status) {
                                        '0' => 'Not Received',
                                        '1' => 'Fully Received',
                                        '2' => 'Partial',
                                        default => 'Unknown'
                                    };
                                @endphp
                                <span class="badge bg-{{ $deliveryClass }}">
                                    {{ $deliveryText }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.porder.show', $po->porder_id) }}" 
                                       class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.porder.edit', $po->porder_id) }}" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.porder.pdf', $po->porder_id) }}" 
                                       class="btn btn-secondary" title="PDF" target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger delete-btn" 
                                            data-id="{{ $po->porder_id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No purchase orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this purchase order?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Delete button click
        $('.delete-btn').on('click', function() {
            const id = $(this).data('id');
            $('#deleteForm').attr('action', '{{ route("admin.porder.destroy", "") }}/' + id);
            $('#deleteModal').modal('show');
        });
    });
</script>
@endpush
