@extends('layouts.admin')

@section('title', 'Edit Receive Order — ' . $receiveOrder->rorder_slip_no)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Receive Order &mdash; {{ $receiveOrder->rorder_slip_no }}</h4>
        <a href="{{ route('admin.receive.show', $receiveOrder->rorder_id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Receive Order
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
                    @if($receiveOrder->purchaseOrder)
                        <a href="{{ route('admin.porder.show', $receiveOrder->purchaseOrder->porder_id) }}">
                            {{ $receiveOrder->purchaseOrder->porder_no }}
                        </a>
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <strong>Project:</strong><br>
                    {{ $receiveOrder->purchaseOrder->project->proj_name ?? '—' }}
                </div>
                <div class="col-md-3">
                    <strong>Supplier:</strong><br>
                    {{ $receiveOrder->purchaseOrder->supplier->sup_name ?? '—' }}
                </div>
                <div class="col-md-3">
                    <strong>Slip Number:</strong><br>
                    <span class="fs-5">{{ $receiveOrder->rorder_slip_no }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Form --}}
    <form method="POST" action="{{ route('admin.receive.update', $receiveOrder->rorder_id) }}" id="editReceiveForm">
        @csrf
        @method('PUT')

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
                                   id="slip_no" name="slip_no"
                                   value="{{ old('slip_no', $receiveOrder->rorder_slip_no) }}"
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
                                   value="{{ old('receive_date', \Carbon\Carbon::parse($receiveOrder->rorder_date)->format('Y-m-d')) }}"
                                   required>
                            @error('receive_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <input type="text" class="form-control" id="notes" name="notes"
                                   value="{{ old('notes', $receiveOrder->rorder_infoset) }}"
                                   placeholder="Optional notes">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Received Items</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th class="text-center">Ordered Qty</th>
                                <th class="text-center">Current Received</th>
                                <th class="text-center" style="width: 150px;">Receive Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Build lookup of current RO items by item code
                                $roItemsLookup = $receiveOrder->items->keyBy('ro_detail_item');
                            @endphp
                            @foreach($receiveOrder->purchaseOrder->items as $poItem)
                                @php
                                    $itemCode = $poItem->po_detail_item;
                                    $orderedQty = $poItem->po_detail_qty;
                                    $currentRoItem = $roItemsLookup->get($itemCode);
                                    $currentQty = $currentRoItem ? $currentRoItem->ro_detail_quantity : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $itemCode }}</strong>
                                        <input type="hidden" name="items[{{ $itemCode }}][item_code]" value="{{ $itemCode }}">
                                    </td>
                                    <td>{{ $poItem->po_detail_itemname ?? $poItem->item->item_name ?? '—' }}</td>
                                    <td class="text-center">{{ $orderedQty }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $currentQty }}</span>
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                               class="form-control form-control-sm text-center receive-qty"
                                               name="items[{{ $itemCode }}][quantity]"
                                               value="{{ old('items.' . $itemCode . '.quantity', $currentQty) }}"
                                               min="0" max="{{ $orderedQty }}" step="1">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary" id="btnUpdate">
                <i class="fas fa-save me-1"></i> Update Receive Order
            </button>
            <a href="{{ route('admin.receive.show', $receiveOrder->rorder_id) }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Validate at least one item has a quantity > 0
    $('#editReceiveForm').on('submit', function(e) {
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

    // Prevent exceeding ordered quantity
    $('.receive-qty').on('change', function() {
        var max = parseInt($(this).attr('max'));
        var val = parseInt($(this).val());

        if (val > max) {
            $(this).val(max);
            alert('Receive quantity cannot exceed ordered quantity (' + max + ').');
        }
        if (val < 0) {
            $(this).val(0);
        }
    });
});
</script>
@endpush
@endsection
