@extends('layouts.admin')

@section('title', 'Compare Quotes')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-balance-scale"></i> Compare Quotes &mdash; {{ $rfq->rfq_no }}
        </h1>
        <a href="{{ route('admin.rfq.show', $rfq->rfq_id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to RFQ
        </a>
    </div>

    {{-- Comparison Matrix --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table me-1"></i> Quote Comparison Matrix
            </h6>
        </div>
        <div class="card-body">
            @if($rfq->suppliers->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">No suppliers have been invited to this RFQ yet.</p>
                </div>
            @elseif($rfq->items->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">No items have been added to this RFQ yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0" id="compareTable">
                        <thead class="table-light">
                            <tr>
                                <th class="bg-light" style="min-width: 200px;">Item</th>
                                @foreach($rfq->suppliers as $rfqSupplier)
                                    <th class="text-center" style="min-width: 150px;">
                                        {{ $rfqSupplier->supplier->sup_name ?? 'Supplier #' . $rfqSupplier->rfqs_supplier_id }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rfq->items as $rfqItem)
                                @php
                                    // Collect all quoted prices for this item across all suppliers
                                    $quotedPrices = [];
                                    foreach ($rfq->suppliers as $rfqSupplier) {
                                        $quote = $rfqItem->quotes->first(function ($q) use ($rfqSupplier) {
                                            return $q->rfq_supplier_id === $rfqSupplier->id;
                                        });
                                        $quotedPrices[$rfqSupplier->id] = $quote ? $quote->quoted_price : null;
                                    }
                                    // Find the lowest non-null price
                                    $validPrices = array_filter($quotedPrices, fn($p) => $p !== null);
                                    $lowestPrice = !empty($validPrices) ? min($validPrices) : null;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $rfqItem->item->item_name ?? 'Item #' . $rfqItem->rfqi_item_id }}</strong>
                                        <br><small class="text-muted">Qty: {{ $rfqItem->rfqi_quantity }}</small>
                                    </td>
                                    @foreach($rfq->suppliers as $rfqSupplier)
                                        @php
                                            $price = $quotedPrices[$rfqSupplier->id];
                                            $isLowest = $price !== null && $lowestPrice !== null && $price == $lowestPrice;
                                        @endphp
                                        <td class="text-center {{ $isLowest ? 'table-success fw-bold' : '' }}">
                                            @if($price !== null)
                                                ${{ number_format($price, 2) }}
                                                @if($isLowest)
                                                    <i class="fas fa-check-circle text-success ms-1" title="Lowest price"></i>
                                                @endif
                                            @else
                                                <span class="text-muted">&mdash;</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            {{-- Totals Row --}}
                            <tr class="fw-bold">
                                <td class="bg-light"><strong>Total</strong></td>
                                @php
                                    $supplierTotals = [];
                                    foreach ($rfq->suppliers as $rfqSupplier) {
                                        $total = 0;
                                        $hasQuotes = false;
                                        foreach ($rfq->items as $rfqItem) {
                                            $quote = $rfqItem->quotes->first(function ($q) use ($rfqSupplier) {
                                                return $q->rfq_supplier_id === $rfqSupplier->id;
                                            });
                                            if ($quote && $quote->quoted_price !== null) {
                                                $total += $quote->quoted_price;
                                                $hasQuotes = true;
                                            }
                                        }
                                        $supplierTotals[$rfqSupplier->id] = $hasQuotes ? $total : null;
                                    }
                                    $validTotals = array_filter($supplierTotals, fn($t) => $t !== null);
                                    $lowestTotal = !empty($validTotals) ? min($validTotals) : null;
                                @endphp
                                @foreach($rfq->suppliers as $rfqSupplier)
                                    @php
                                        $total = $supplierTotals[$rfqSupplier->id];
                                        $isLowestTotal = $total !== null && $lowestTotal !== null && $total == $lowestTotal;
                                    @endphp
                                    <td class="text-center {{ $isLowestTotal ? 'table-success' : '' }}">
                                        @if($total !== null)
                                            ${{ number_format($total, 2) }}
                                            @if($isLowestTotal)
                                                <i class="fas fa-trophy text-warning ms-1" title="Lowest total"></i>
                                            @endif
                                        @else
                                            <span class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Action Row --}}
                            <tr>
                                <td class="bg-light"><strong>Action</strong></td>
                                @foreach($rfq->suppliers as $rfqSupplier)
                                    <td class="text-center">
                                        <form method="POST" action="{{ route('admin.rfq.converttopo', $rfq->rfq_id) }}"
                                              onsubmit="return confirm('Select {{ $rfqSupplier->supplier->sup_name ?? 'this supplier' }} as the winner and convert to a Purchase Order?');">
                                            @csrf
                                            <input type="hidden" name="rfq_id" value="{{ $rfq->rfq_id }}">
                                            <input type="hidden" name="rfq_supplier_id" value="{{ $rfqSupplier->id }}">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-trophy me-1"></i> Select as Winner
                                            </button>
                                        </form>
                                    </td>
                                @endforeach
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Highlight row on hover for better readability in the comparison matrix
        $('#compareTable tbody tr').hover(
            function() { $(this).addClass('table-active'); },
            function() { $(this).removeClass('table-active'); }
        );
    });
</script>
@endpush
