@extends('layouts.admin')

@section('title', 'Price Comparison - ' . $item->item_name)

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Price Comparison</h1>
            <p class="text-muted mb-0">Comparing supplier prices for <strong>{{ $item->item_name }}</strong> ({{ $item->item_code }}).</p>
        </div>
        <a href="{{ route('admin.item.show', $item->item_id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Item
        </a>
    </div>

    <!-- Price Comparison Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Supplier Prices</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="priceComparisonTable">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $lowestPrice = $comparison->min('supcat_price');
                        @endphp
                        @forelse($comparison as $entry)
                            <tr class="{{ $entry->supcat_price == $lowestPrice ? 'table-success' : '' }}">
                                <td>
                                    {{ $entry->sup_name }}
                                    @if($entry->supcat_price == $lowestPrice)
                                        <span class="badge bg-success ms-1">Lowest</span>
                                    @endif
                                </td>
                                <td>{{ $entry->supcat_sku_no ?? '-' }}</td>
                                <td><strong>${{ number_format($entry->supcat_price, 2) }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($entry->updated_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No supplier pricing data available for this item.</td>
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
    $(function () {
        if ($.fn.DataTable) {
            $('#priceComparisonTable').DataTable({
                paging: false,
                info: false,
                order: [[2, 'asc']],
                columnDefs: [
                    { orderable: true, targets: '_all' }
                ]
            });
        }
    });
</script>
@endpush
