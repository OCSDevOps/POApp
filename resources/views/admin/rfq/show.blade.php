@extends('layouts.admin')

@section('title', 'RFQ Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">{{ $rfq->rfq_no }} — {{ $rfq->rfq_title }}</h4>
        <p class="text-muted mb-0">Project: {{ $rfq->project->proj_name ?? 'N/A' }} | Due: {{ optional($rfq->rfq_due_date)->format('Y-m-d') }}</p>
    </div>
    @if($rfq->rfq_status == \App\Models\Rfq::STATUS_DRAFT)
        <form method="POST" action="{{ route('admin.rfq.send', $rfq->rfq_id) }}">
            @csrf
            <button class="btn btn-primary btn-sm" type="submit">Send RFQ</button>
        </form>
    @endif
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">Items</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>UOM</th>
                            <th>Target</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rfq->items as $item)
                            <tr>
                                <td>{{ $item->item->item_name ?? 'Item #'.$item->rfqi_item_id }}</td>
                                <td>{{ $item->rfqi_quantity }}</td>
                                <td>{{ $item->unitOfMeasure->uom_name ?? '' }}</td>
                                <td>{{ $item->rfqi_target_price ? '$'.number_format($item->rfqi_target_price,2) : '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Quotes</div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Status</th>
                            <th>Quoted Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rfq->suppliers as $rfqSupplier)
                            <tr>
                                <td>{{ $rfqSupplier->supplier->sup_name ?? 'Supplier #'.$rfqSupplier->rfqs_supplier_id }}</td>
                                <td>{{ $rfqSupplier->status_text }}</td>
                                <td>${{ number_format($rfqSupplier->total_quoted_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-2">Summary</h6>
                <p class="mb-1"><strong>Status:</strong> {{ $rfq->status_text }}</p>
                <p class="mb-1"><strong>Created at:</strong> {{ optional($rfq->rfq_created_at)->format('Y-m-d H:i') }}</p>
                <p class="mb-1"><strong>Last updated:</strong> {{ optional($rfq->rfq_modified_at)->format('Y-m-d H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
