@extends('layouts.admin')

@section('title', 'Create PO Change Order - ' . $purchaseOrder->porder_no)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Create PO Change Order: {{ $purchaseOrder->porder_no }}</h4>
                    <a href="{{ route('admin.porder.show', $purchaseOrder->porder_id) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to PO
                    </a>
                </div>

                <div class="card-body">
                    @include('partials.validation-errors')
                    <!-- Current PO Summary -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Project:</strong><br>
                                    {{ $purchaseOrder->project->proj_name }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Supplier:</strong><br>
                                    {{ $purchaseOrder->supplier->sup_name }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Current Total:</strong><br>
                                    <span class="fs-5">${{ number_format($purchaseOrder->porder_total_amount, 2) }}</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Status:</strong><br>
                                    <span class="badge bg-info">{{ $purchaseOrder->porder_status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.po-change-orders.store', $purchaseOrder->porder_id) }}" id="pcoForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Change Order Type <span class="text-danger">*</span></label>
                                    <select name="poco_type" id="poco_type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        <option value="amount_change">Amount Change</option>
                                        <option value="item_change">Item Change</option>
                                        <option value="date_change">Date Change</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">New PO Total <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="new_total" id="new_total" class="form-control" 
                                               step="0.01" min="0" placeholder="0.00" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Current PO Total</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" id="current_total" class="form-control" 
                                               value="{{ number_format($purchaseOrder->porder_total_amount, 2) }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Change Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" id="change_amount" class="form-control" readonly>
                                    </div>
                                    <div id="budget_warning" class="mt-2" style="display: none;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reason for Change <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control" rows="4" 
                                      placeholder="Explain why this PO change is needed..." required></textarea>
                            <small class="text-muted">Maximum 1000 characters</small>
                        </div>

                        <!-- Details for specific change types -->
                        <div id="itemChangeDetails" style="display: none;">
                            <div class="card bg-light mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Item Change Details (Optional)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <label class="form-label">Items Added/Removed/Modified</label>
                                        <textarea class="form-control" rows="3" id="item_details" 
                                                  placeholder="List items that were added, removed, or modified..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="dateChangeDetails" style="display: none;">
                            <div class="card bg-light mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Date Change Details (Optional)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Previous Delivery Date</label>
                                            <input type="date" class="form-control" id="old_delivery_date">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">New Delivery Date</label>
                                            <input type="date" class="form-control" id="new_delivery_date">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="details" id="details_json">

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create PO Change Order
                            </button>
                            <a href="{{ route('admin.porder.show', $purchaseOrder->porder_id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const currentTotal = parseFloat($('#current_total').val().replace(/,/g, ''));

    // Calculate change amount
    $('#new_total').on('input', function() {
        const newTotal = parseFloat($(this).val()) || 0;
        const change = newTotal - currentTotal;
        
        $('#change_amount').val((change >= 0 ? '+' : '') + change.toFixed(2))
            .toggleClass('text-success', change >= 0)
            .toggleClass('text-danger', change < 0);

        // Check budget if amount is increasing
        if (change > 0) {
            checkBudget();
        } else {
            $('#budget_warning').hide();
        }
    });

    // Show/hide detail sections based on type
    $('#poco_type').change(function() {
        const type = $(this).val();
        $('#itemChangeDetails, #dateChangeDetails').hide();
        
        if (type === 'item_change') {
            $('#itemChangeDetails').show();
        } else if (type === 'date_change') {
            $('#dateChangeDetails').show();
        }
    });

    // Check budget availability
    function checkBudget() {
        // This would call the budget check endpoint
        // For now, just show a placeholder
        $('#budget_warning').html(
            '<div class="alert alert-info">' +
            '<i class="fas fa-info-circle"></i> Budget availability will be checked before submission.' +
            '</div>'
        ).show();
    }

    // Prepare details JSON before submit
    $('#pcoForm').on('submit', function() {
        const type = $('#poco_type').val();
        let details = {};

        if (type === 'item_change') {
            details.item_details = $('#item_details').val();
        } else if (type === 'date_change') {
            details.old_delivery_date = $('#old_delivery_date').val();
            details.new_delivery_date = $('#new_delivery_date').val();
        }

        $('#details_json').val(JSON.stringify(details));
    });
});
</script>
@endpush
@endsection
