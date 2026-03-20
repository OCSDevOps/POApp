@extends('layouts.admin')

@section('title', 'Price History - ' . $item->item_name)

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Price History</h1>
            <p class="text-muted mb-0">All price changes for <strong>{{ $item->item_name }}</strong> ({{ $item->item_code }}).</p>
        </div>
        <a href="{{ route('admin.item.show', $item->item_id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Item
        </a>
    </div>

    <!-- Price History Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Price Change Log</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="priceHistoryTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Old Price</th>
                            <th>New Price</th>
                            <th>Change %</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $record)
                            @php
                                $changePercent = $record->iph_old_price > 0
                                    ? (($record->iph_new_price - $record->iph_old_price) / $record->iph_old_price) * 100
                                    : 0;
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($record->iph_effective_date)->format('M d, Y') }}</td>
                                <td>{{ $record->supplier->sup_name ?? '-' }}</td>
                                <td>${{ number_format($record->iph_old_price, 2) }}</td>
                                <td>${{ number_format($record->iph_new_price, 2) }}</td>
                                <td>
                                    @if($changePercent > 0)
                                        <span class="text-danger">
                                            <i class="fas fa-arrow-up me-1"></i>+{{ number_format($changePercent, 1) }}%
                                        </span>
                                    @elseif($changePercent < 0)
                                        <span class="text-success">
                                            <i class="fas fa-arrow-down me-1"></i>{{ number_format($changePercent, 1) }}%
                                        </span>
                                    @else
                                        <span class="text-muted">0.0%</span>
                                    @endif
                                </td>
                                <td>{{ $record->iph_notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No price history records found for this item.</td>
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
            $('#priceHistoryTable').DataTable({
                paging: false,
                info: false,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: true, targets: '_all' }
                ]
            });
        }
    });
</script>
@endpush
