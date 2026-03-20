@extends('layouts.admin')

@section('title', $item->item_name)

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                {{ $item->item_name }}
                <small class="text-muted">({{ $item->item_code }})</small>
                @if($item->item_status)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Inactive</span>
                @endif
            </h1>
        </div>
        <div>
            <a href="{{ route('admin.item.edit', $item->item_id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.item.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <button type="button" class="btn btn-danger delete-btn"
                    data-url="{{ route('admin.item.destroy', $item->item_id) }}" data-name="{{ $item->item_name }}">
                <i class="fas fa-trash me-1"></i> Delete
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Item Details Card -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Item Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="30%">Code:</th>
                                <td>{{ $item->item_code }}</td>
                            </tr>
                            <tr>
                                <th>Name:</th>
                                <td>{{ $item->item_name }}</td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td>{{ $item->item_description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Category:</th>
                                <td>{{ $item->category->icat_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Cost Code:</th>
                                <td>
                                    @if($item->costCode)
                                        {{ $item->costCode->cc_no }} - {{ $item->costCode->cc_description }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Unit of Measure:</th>
                                <td>{{ $item->unitOfMeasure->uom_name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($item->item_status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Links Card -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pricing Tools</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.item.pricecomparison', $item->item_id) }}" class="btn btn-outline-primary mb-2 d-block text-start">
                        <i class="fas fa-balance-scale me-2"></i> View Price Comparison
                    </a>
                    <a href="{{ route('admin.item.pricehistory', $item->item_id) }}" class="btn btn-outline-info mb-2 d-block text-start">
                        <i class="fas fa-history me-2"></i> View Full Price History
                    </a>
                    <a href="{{ route('admin.item.pricingsummary') }}" class="btn btn-outline-success d-block text-start">
                        <i class="fas fa-chart-bar me-2"></i> Pricing Summary Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Catalog Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Supplier Catalog</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Supplier Name</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Lead Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplierCatalog as $entry)
                            <tr>
                                <td>{{ $entry->supplier->sup_name ?? '-' }}</td>
                                <td>{{ $entry->supcat_sku_no ?? '-' }}</td>
                                <td>${{ number_format($entry->supcat_price, 2) }}</td>
                                <td>{{ $entry->supcat_lead_days ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No suppliers currently offer this item.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Price History Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Price History</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Old Price</th>
                            <th>New Price</th>
                            <th>Effective Date</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($priceHistory as $history)
                            <tr>
                                <td>{{ $history->supplier->sup_name ?? '-' }}</td>
                                <td>${{ number_format($history->iph_old_price, 2) }}</td>
                                <td>${{ number_format($history->iph_new_price, 2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($history->iph_effective_date)->format('M d, Y') }}</td>
                                <td>{{ $history->iph_notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No price history available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@include('partials.delete-modal')
@endsection

@push('scripts')
<script>
    // Placeholder for any show-page enhancements
</script>
@endpush
