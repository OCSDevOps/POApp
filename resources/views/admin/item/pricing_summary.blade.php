@extends('layouts.admin')

@section('title', 'Pricing Summary')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Pricing Summary</h1>
            <p class="text-muted mb-0">Aggregated pricing data across all items and suppliers.</p>
        </div>
        <a href="{{ route('admin.item.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Items
        </a>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.item.pricingsummary') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->icat_id }}" @selected(request('category') == $category->icat_id)>
                                    {{ $category->icat_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Item code or name..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-1">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.item.pricingsummary') }}" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Pricing Summary Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Summary</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="pricingSummaryTable">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Avg Price</th>
                            <th>Min Price</th>
                            <th>Max Price</th>
                            <th>Supplier Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($summary as $row)
                            <tr>
                                <td><strong>{{ $row->item_code }}</strong></td>
                                <td>{{ $row->item_name }}</td>
                                <td>{{ $row->category_name ?? '-' }}</td>
                                <td>${{ number_format($row->avg_price, 2) }}</td>
                                <td>${{ number_format($row->min_price, 2) }}</td>
                                <td>${{ number_format($row->max_price, 2) }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $row->supplier_count }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No pricing summary data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $summary->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        if ($.fn.DataTable) {
            $('#pricingSummaryTable').DataTable({
                paging: false,
                info: false,
                searching: false,
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: true, targets: '_all' }
                ]
            });
        }
    });
</script>
@endpush
