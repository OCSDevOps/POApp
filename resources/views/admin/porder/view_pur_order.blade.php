@extends('layouts.admin')

@section('title', 'View Purchase Order')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            {{ $purchaseOrder->porder_no }}
            @php
                $statusClass = match((int) $purchaseOrder->porder_status) {
                    1 => 'success',
                    0 => 'secondary',
                    default => 'secondary'
                };
                $statusText = match((int) $purchaseOrder->porder_status) {
                    1 => 'Active',
                    0 => 'Inactive',
                    default => 'Unknown'
                };
            @endphp
            <span class="badge bg-{{ $statusClass }}">
                {{ $statusText }}
            </span>
        </h1>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.porder.edit', $purchaseOrder->porder_id) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('admin.porder.pdf', $purchaseOrder->porder_id) }}" class="btn btn-secondary" target="_blank">
            <i class="fas fa-file-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('admin.porder.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <button type="button" class="btn btn-danger" id="deleteBtn" data-id="{{ $purchaseOrder->porder_id }}">
            <i class="fas fa-trash me-1"></i> Delete
        </button>
    </div>
</div>

<div class="row">
    {{-- Left Column --}}
    <div class="col-lg-8">
        {{-- PO Details Card --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle me-1"></i> PO Details
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Project</label>
                        <p class="mb-0 fw-semibold">{{ $purchaseOrder->project->proj_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Supplier</label>
                        <p class="mb-0 fw-semibold">{{ $purchaseOrder->supplier->sup_name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Created Date</label>
                        <p class="mb-0">{{ $purchaseOrder->porder_createdate ? date('M d, Y', strtotime($purchaseOrder->porder_createdate)) : 'N/A' }}</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Address</label>
                        <p class="mb-0">{{ $purchaseOrder->porder_address ?? 'N/A' }}</p>
                    </div>
                    @if($purchaseOrder->porder_description)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Description</label>
                            <p class="mb-0">{{ $purchaseOrder->porder_description }}</p>
                        </div>
                    @endif
                    @if($purchaseOrder->porder_delivery_note)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Delivery Note</label>
                            <p class="mb-0">{{ $purchaseOrder->porder_delivery_note }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Attachments --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-paperclip me-1"></i> Attachments
            </div>
            <div class="card-body">
                @forelse($purchaseOrder->attachments as $attachment)
                    <div class="d-flex justify-content-between align-items-center border rounded px-3 py-2 mb-2">
                        <div>
                            <div class="fw-semibold">
                                <i class="fas fa-file me-1 text-secondary"></i>
                                {{ $attachment->po_attachment_original_name }}
                            </div>
                            <small class="text-muted">
                                {{ number_format(($attachment->po_attachment_size ?? 0) / 1024, 1) }} KB
                            </small>
                        </div>
                        <a
                            href="{{ route('admin.porder.attachments.download', [$purchaseOrder->porder_id, $attachment->po_attachment_id]) }}"
                            class="btn btn-sm btn-outline-primary"
                        >
                            <i class="fas fa-download me-1"></i> Download
                        </a>
                    </div>
                @empty
                    <p class="text-muted mb-0">No attachments uploaded.</p>
                @endforelse
            </div>
        </div>

        {{-- Items Table --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-list me-1"></i> Line Items
            </div>
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Tax</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrder->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->po_detail_item }}</td>
                                <td>{{ $item->po_detail_sku }}</td>
                                <td class="text-end">{{ $item->po_detail_quantity }}</td>
                                <td class="text-end">${{ number_format($item->po_detail_unitprice, 2) }}</td>
                                <td class="text-end">${{ number_format($item->po_detail_taxamount, 2) }}</td>
                                <td class="text-end">${{ number_format($item->po_detail_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($purchaseOrder->items->count() > 0)
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5"></td>
                                <td class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end"><strong>${{ number_format($purchaseOrder->porder_total_amount, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td class="text-end"><strong>Tax:</strong></td>
                                <td class="text-end"><strong>${{ number_format($purchaseOrder->porder_total_tax, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="5"></td>
                                <td class="text-end"><strong>Grand Total:</strong></td>
                                <td class="text-end"><strong>${{ number_format($purchaseOrder->grand_total, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="col-lg-4">
        {{-- Summary Card --}}
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-clipboard-list me-1"></i> Summary
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Status</label>
                    <div>
                        @php
                            $statusClass = match((int) $purchaseOrder->porder_status) {
                                1 => 'success',
                                0 => 'secondary',
                                default => 'secondary'
                            };
                            $statusText = match((int) $purchaseOrder->porder_status) {
                                1 => 'Active',
                                0 => 'Inactive',
                                default => 'Unknown'
                            };
                        @endphp
                        <span class="badge bg-{{ $statusClass }} fs-6">
                            {{ $statusText }}
                        </span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Delivery Status</label>
                    <div>
                        @php
                            $deliveryClass = match($purchaseOrder->porder_delivery_status) {
                                '0' => 'danger',
                                '1' => 'success',
                                '2' => 'warning',
                                default => 'secondary'
                            };
                            $deliveryText = match($purchaseOrder->porder_delivery_status) {
                                '0' => 'Not Received',
                                '1' => 'Fully Received',
                                '2' => 'Partial',
                                default => 'Unknown'
                            };
                        @endphp
                        <span class="badge bg-{{ $deliveryClass }} fs-6">
                            {{ $deliveryText }}
                        </span>
                    </div>
                </div>
                <hr>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Subtotal</span>
                        <strong>${{ number_format($purchaseOrder->porder_total_amount, 2) }}</strong>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Tax</span>
                        <strong>${{ number_format($purchaseOrder->porder_total_tax, 2) }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between border-top pt-2">
                        <span class="text-muted fw-bold">Grand Total</span>
                        <strong class="text-primary fs-5">${{ number_format($purchaseOrder->grand_total, 2) }}</strong>
                    </div>
                </div>
                <hr>
                <div class="mb-2">
                    <label class="text-muted small">Created</label>
                    <p class="mb-0">{{ $purchaseOrder->porder_createdate ? date('M d, Y h:i A', strtotime($purchaseOrder->porder_createdate)) : 'N/A' }}</p>
                </div>
                <div class="mb-0">
                    <label class="text-muted small">Last Modified</label>
                    <p class="mb-0">{{ $purchaseOrder->porder_modifydate ? date('M d, Y h:i A', strtotime($purchaseOrder->porder_modifydate)) : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this purchase order (<strong>{{ $purchaseOrder->porder_no }}</strong>)? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('admin.porder.destroy', $purchaseOrder->porder_id) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#deleteBtn').on('click', function() {
            $('#deleteModal').modal('show');
        });
    });
</script>
@endpush
@endsection
