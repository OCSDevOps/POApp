@extends('layouts.admin')

@section('title', 'Receive Items — PO ' . $purchaseOrder->porder_no)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fas fa-truck-loading me-2"></i>Receive Items &mdash; PO {{ $purchaseOrder->porder_no }}</h4>
        <a href="{{ route('admin.receive.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Receive Orders
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- PO Information Card --}}
    <div class="card shadow-sm mb-3">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Purchase Order Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>PO Number:</strong><br>
                    <span class="fs-5">{{ $purchaseOrder->porder_no }}</span>
                </div>
                <div class="col-md-3">
                    <strong>Project:</strong><br>
                    {{ $purchaseOrder->project->proj_name ?? '—' }}
                </div>
                <div class="col-md-3">
                    <strong>Supplier:</strong><br>
                    {{ $purchaseOrder->supplier->sup_name ?? '—' }}
                </div>
                <div class="col-md-3">
                    <strong>PO Date:</strong><br>
                    {{ \Carbon\Carbon::parse($purchaseOrder->porder_createdate)->format('m/d/Y') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Receive Form --}}
    <form method="POST" action="{{ route('admin.receive.store') }}" id="receiveForm">
        @csrf
        <input type="hidden" name="po_id" value="{{ $purchaseOrder->porder_id }}">

        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Receive Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="slip_no" class="form-label">Slip Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('slip_no') is-invalid @enderror"
                                   id="slip_no" name="slip_no" value="{{ old('slip_no') }}"
                                   placeholder="Enter delivery slip number" required>
                            @error('slip_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="receive_date" class="form-label">Receive Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('receive_date') is-invalid @enderror"
                                   id="receive_date" name="receive_date"
                                   value="{{ old('receive_date', date('Y-m-d')) }}" required>
                            @error('receive_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <input type="text" class="form-control" id="notes" name="notes"
                                   value="{{ old('notes') }}" placeholder="Optional notes">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Items to Receive</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th class="text-center">Ordered Qty</th>
                                <th class="text-center">Previously Received</th>
                                <th class="text-center">Remaining</th>
                                <th class="text-center" style="width: 150px;">Receive Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrder->items as $item)
                                @php
                                    $itemCode = $item->po_detail_item;
                                    $orderedQty = $item->po_detail_qty;
                                    $previouslyReceived = $receivedQtys[$itemCode] ?? 0;
                                    $remaining = $orderedQty - $previouslyReceived;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $itemCode }}</strong>
                                        <input type="hidden" name="items[{{ $itemCode }}][item_code]" value="{{ $itemCode }}">
                                    </td>
                                    <td>{{ $item->po_detail_itemname ?? $item->item->item_name ?? '—' }}</td>
                                    <td class="text-center">{{ $orderedQty }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $previouslyReceived }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $remaining > 0 ? 'bg-warning' : 'bg-success' }}">
                                            {{ $remaining }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($remaining > 0)
                                            <input type="number"
                                                   class="form-control form-control-sm text-center receive-qty"
                                                   name="items[{{ $itemCode }}][quantity]"
                                                   value="{{ old('items.' . $itemCode . '.quantity', 0) }}"
                                                   min="0" max="{{ $remaining }}" step="1">
                                        @else
                                            <span class="text-success"><i class="fas fa-check-circle"></i> Fully Received</span>
                                            <input type="hidden" name="items[{{ $itemCode }}][quantity]" value="0">
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary" id="btnSubmit">
                <i class="fas fa-save me-1"></i> Create Receive Order
            </button>
            <a href="{{ route('admin.receive.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Validate at least one item has a quantity > 0
    $('#receiveForm').on('submit', function(e) {
        var hasItems = false;
        $('.receive-qty').each(function() {
            if (parseInt($(this).val()) > 0) {
                hasItems = true;
                return false;
            }
        });

        if (!hasItems) {
            e.preventDefault();
            alert('Please enter a receive quantity for at least one item.');
            return false;
        }
    });

    // Prevent exceeding remaining quantity
    $('.receive-qty').on('change', function() {
        var max = parseInt($(this).attr('max'));
        var val = parseInt($(this).val());

        if (val > max) {
            $(this).val(max);
            alert('Receive quantity cannot exceed remaining quantity (' + max + ').');
        }
        if (val < 0) {
            $(this).val(0);
        }
    });
});
</script>
@endpush
@endsection
