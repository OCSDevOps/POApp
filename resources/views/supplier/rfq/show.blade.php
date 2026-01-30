@extends('supplier.layouts.app')

@section('title', 'RFQ Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">{{ $rfq->rfq_no }} — {{ $rfq->rfq_title }}</h4>
        <p class="text-muted mb-0">Project: {{ $rfq->project->proj_name ?? 'N/A' }} | Due: {{ optional($rfq->rfq_due_date)->format('Y-m-d') }}</p>
    </div>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="card mb-4">
    <div class="card-header">Line Items</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>UOM</th>
                    <th>Target Price</th>
                    <th>Your Price</th>
                    <th>Lead Time (days)</th>
                </tr>
            </thead>
            <tbody>
                <form method="POST" action="{{ route('supplier.rfq.quote', $rfq->rfq_id) }}">
                    @csrf
                    @foreach($rfq->items as $item)
                        <tr>
                            <td>{{ $item->item->item_name ?? 'Item #'.$item->rfqi_item_id }}</td>
                            <td>{{ $item->rfqi_quantity }}</td>
                            <td>{{ $item->unitOfMeasure->uom_name ?? '' }}</td>
                            <td>{{ $item->rfqi_target_price ? '$'.number_format($item->rfqi_target_price,2) : '—' }}</td>
                            <td>
                                <input type="hidden" name="quotes[{{ $loop->index }}][rfq_item_id]" value="{{ $item->rfqi_id }}">
                                <input type="number" step="0.01" class="form-control form-control-sm" name="quotes[{{ $loop->index }}][price]" required>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm" name="quotes[{{ $loop->index }}][lead_time_days]" min="0">
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="6" class="text-end">
                            <button class="btn btn-primary btn-sm" type="submit">Submit Quote</button>
                        </td>
                    </tr>
                </form>
            </tbody>
        </table>
    </div>
</div>
@endsection
