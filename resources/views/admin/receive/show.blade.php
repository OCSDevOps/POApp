@extends('layouts.admin')

@section('title', 'Receive Order — ' . $receiveOrder->rorder_slip_no)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fas fa-truck-loading me-2"></i>Receive Order {{ $receiveOrder->rorder_slip_no }}</h4>
        <div>
            <a href="{{ route('admin.receive.edit', $receiveOrder->rorder_id) }}" class="btn btn-warning btn-sm me-1">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.receive.index') }}" class="btn btn-secondary btn-sm me-1">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
            <form method="POST" action="{{ route('admin.receive.destroy', $receiveOrder->rorder_id) }}"
                  class="d-inline" onsubmit="return confirm('Are you sure you want to delete this receive order?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- Left Column: RO Details --}}
        <div class="col-lg-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Receive Order Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%">Slip Number:</th>
                            <td><strong>{{ $receiveOrder->rorder_slip_no }}</strong></td>
                        </tr>
                        <tr>
                            <th>Receive Date:</th>
                            <td>{{ \Carbon\Carbon::parse($receiveOrder->rorder_date)->format('m/d/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge
                                    @if($receiveOrder->rorder_status == 1) bg-success
                                    @elseif($receiveOrder->rorder_status == 0) bg-secondary
                                    @else bg-info
                                    @endif">
                                    @if($receiveOrder->rorder_status == 1) Active
                                    @elseif($receiveOrder->rorder_status == 0) Inactive
                                    @else {{ $receiveOrder->rorder_status }}
                                    @endif
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
                            <td><span class="fs-5 fw-bold">${{ number_format($receiveOrder->rorder_totalamount, 2) }}</span></td>
                        </tr>
                        @if($receiveOrder->rorder_infoset)
                        <tr>
                            <th>Notes:</th>
                            <td>{{ $receiveOrder->rorder_infoset }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column: PO Details --}}
        <div class="col-lg-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Purchase Order Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="40%">PO Number:</th>
                            <td>
                                @if($receiveOrder->purchaseOrder)
                                    <a href="{{ route('admin.porder.show', $receiveOrder->purchaseOrder->porder_id) }}">
                                        <strong>{{ $receiveOrder->purchaseOrder->porder_no }}</strong>
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Project:</th>
                            <td>{{ $receiveOrder->purchaseOrder->project->proj_name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Supplier:</th>
                            <td>{{ $receiveOrder->purchaseOrder->supplier->sup_name ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Items Received</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th class="text-center">Quantity Received</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receiveOrder->items as $index => $roItem)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><strong>{{ $roItem->ro_detail_item }}</strong></td>
                                <td>{{ $roItem->item->item_name ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary fs-6">{{ $roItem->ro_detail_quantity }}</span>
                                </td>
                                <td>
                                    <span class="badge
                                        @if($roItem->ro_detail_status == 1) bg-success
                                        @elseif($roItem->ro_detail_status == 0) bg-secondary
                                        @else bg-info
                                        @endif">
                                        @if($roItem->ro_detail_status == 1) Active
                                        @elseif($roItem->ro_detail_status == 0) Inactive
                                        @else {{ $roItem->ro_detail_status }}
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No items recorded for this receive order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // No additional scripts required for show view
});
</script>
@endpush
@endsection
