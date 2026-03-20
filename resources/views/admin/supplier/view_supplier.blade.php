@extends('layouts.admin')

@section('title', 'View Supplier - ' . $supplier->sup_name)

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-truck me-2"></i>{{ $supplier->sup_name }}
                @if($supplier->sup_status == 1)
                    <span class="badge bg-success ms-2">Active</span>
                @else
                    <span class="badge bg-secondary ms-2">Inactive</span>
                @endif
            </h1>
        </div>
        <div>
            <a href="{{ route('admin.suppliers.edit', $supplier->sup_id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Suppliers
            </a>
        </div>
    </div>

    {{-- Supplier Details Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle me-1"></i> Supplier Details
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th class="text-muted" style="width: 40%;">Supplier ID</th>
                                <td>{{ $supplier->sup_id }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Supplier Name</th>
                                <td>{{ $supplier->sup_name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Contact Person</th>
                                <td>{{ $supplier->sup_contact_person ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Email</th>
                                <td>
                                    @if($supplier->sup_email)
                                        <a href="mailto:{{ $supplier->sup_email }}">{{ $supplier->sup_email }}</a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Phone</th>
                                <td>{{ $supplier->sup_phone ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th class="text-muted" style="width: 40%;">Address</th>
                                <td>{{ $supplier->sup_address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Details / Notes</th>
                                <td>{{ $supplier->sup_details ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Type</th>
                                <td>
                                    @if(($supplier->sup_type ?? 1) == 2)
                                        <span class="badge bg-info">Subcontractor</span>
                                    @elseif(($supplier->sup_type ?? 1) == 3)
                                        <span class="badge bg-primary">Both</span>
                                    @else
                                        <span class="badge bg-secondary">Supplier</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Status</th>
                                <td>
                                    @if($supplier->sup_status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Created At</th>
                                <td>{{ $supplier->sup_createdate ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    @if(in_array($supplier->sup_type ?? 1, [2, 3]))
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('admin.supplier-compliance.index', $supplier->sup_id) }}" class="card shadow text-decoration-none h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3"><i class="fas fa-shield-alt fa-2x text-primary"></i></div>
                    <div>
                        <h6 class="mb-0 text-dark">Compliance & Insurance</h6>
                        <small class="text-muted">View compliance items and insurance certificates</small>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('admin.contracts.index', ['supplier_id' => $supplier->sup_id]) }}" class="card shadow text-decoration-none h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3"><i class="fas fa-file-signature fa-2x text-success"></i></div>
                    <div>
                        <h6 class="mb-0 text-dark">Contracts</h6>
                        <small class="text-muted">View contracts for this subcontractor</small>
                    </div>
                </div>
            </a>
        </div>
    </div>
    @endif

    {{-- Catalog Items Card --}}
    @if($supplier->catalogItems && $supplier->catalogItems->count() > 0)
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-boxes me-1"></i> Catalog Items
                    <span class="badge bg-primary ms-1">{{ $supplier->catalogItems->count() }}</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="catalogItemsTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>Item Name</th>
                                <th>SKU</th>
                                <th class="text-end">Price</th>
                                <th>Lead Days</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($supplier->catalogItems as $catalogItem)
                                <tr>
                                    <td>{{ $catalogItem->item->item_name ?? 'N/A' }}</td>
                                    <td>{{ $catalogItem->catalog_sku ?? 'N/A' }}</td>
                                    <td class="text-end">
                                        @if($catalogItem->catalog_price)
                                            ${{ number_format($catalogItem->catalog_price, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $catalogItem->catalog_lead_days ?? 'N/A' }}</td>
                                    <td>{{ $catalogItem->updated_at ? $catalogItem->updated_at->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        @if(isset($supplier->catalogItems) && $supplier->catalogItems->count() > 0)
            $('#catalogItemsTable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[0, 'asc']]
            });
        @endif
    });
</script>
@endpush
