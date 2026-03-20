@extends('layouts.admin')

@section('title', 'Create Takeoff')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-plus-circle me-1"></i> Create Takeoff
        </h6>
        <a href="{{ route('admin.takeoffs.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Takeoffs
        </a>
    </div>

    @include('partials.validation-errors')

    <form method="POST" action="{{ route('admin.takeoffs.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- Card 1: Takeoff Information --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-1"></i> Takeoff Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="project_id">Project <span class="text-danger">*</span></label>
                        <select class="form-select" id="project_id" name="project_id" required>
                            <option value="">-- Select Project --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->proj_id }}" @selected(old('project_id') == $project->proj_id)>
                                    {{ $project->proj_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="to_title">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="to_title" name="to_title"
                               value="{{ old('to_title') }}" maxlength="250" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="to_description">Description</label>
                    <textarea class="form-control" id="to_description" name="to_description" rows="3">{{ old('to_description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Card 2: Upload Drawings (AI-enabled only) --}}
        @if($aiEnabled)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-file-upload me-1"></i> Upload Drawings
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="drawings">Construction Drawings</label>
                    <input type="file" class="form-control" id="drawings" name="drawings[]"
                           multiple accept=".pdf,.jpg,.jpeg,.png,.tiff,.tif,.bmp">
                    <small class="text-muted">Upload construction drawings (PDF, images). Max 20MB per file.</small>
                </div>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    After creating the takeoff, you can process drawings with AI to auto-extract materials.
                </div>
            </div>
        </div>
        @endif

        {{-- Card 3: Line Items --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list me-1"></i> Material Line Items
                </h6>
                <button type="button" class="btn btn-success btn-sm" id="addRowBtn">
                    <i class="fas fa-plus me-1"></i> Add Row
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="lineItemsTable">
                        <thead>
                            <tr>
                                <th style="width: 12%;">Item Code</th>
                                <th style="width: 20%;">Description <span class="text-danger">*</span></th>
                                <th style="width: 10%;">Qty</th>
                                <th style="width: 12%;">UOM</th>
                                <th style="width: 12%;">Unit Price</th>
                                <th style="width: 18%;">Cost Code</th>
                                <th style="width: 10%;" class="text-end">Line Total</th>
                                <th style="width: 6%;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            <tr class="item-row" data-index="0">
                                <td>
                                    <input type="text" name="items[0][item_code]" class="form-control form-control-sm"
                                           value="{{ old('items.0.item_code') }}">
                                </td>
                                <td>
                                    <input type="text" name="items[0][description]" class="form-control form-control-sm item-description"
                                           value="{{ old('items.0.description') }}" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control form-control-sm item-qty"
                                           value="{{ old('items.0.quantity') }}" min="0" step="0.01">
                                </td>
                                <td>
                                    <select name="items[0][uom_id]" class="form-select form-select-sm">
                                        <option value="">-- UOM --</option>
                                        @foreach($uoms as $uom)
                                            <option value="{{ $uom->uom_id }}" @selected(old('items.0.uom_id') == $uom->uom_id)>
                                                {{ $uom->uom_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][unit_price]" class="form-control form-control-sm item-price"
                                           value="{{ old('items.0.unit_price') }}" step="0.01">
                                </td>
                                <td>
                                    <select name="items[0][cost_code_id]" class="form-select form-select-sm">
                                        <option value="">-- Cost Code --</option>
                                        @foreach($costCodes as $costCode)
                                            <option value="{{ $costCode->cc_id }}" @selected(old('items.0.cost_code_id') == $costCode->cc_id)>
                                                {{ $costCode->cc_no }} - {{ $costCode->cc_description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-end">
                                    <input type="text" class="form-control form-control-sm text-end item-line-total"
                                           value="0.00" readonly>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn" disabled>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Totals --}}
                <div class="row justify-content-end mt-3">
                    <div class="col-md-4">
                        <table class="table table-sm mb-0">
                            <tr>
                                <td class="text-end fw-bold">Subtotal:</td>
                                <td class="text-end" style="width: 150px;">
                                    <span id="subtotalDisplay">$0.00</span>
                                    <input type="hidden" name="subtotal" id="subtotalInput" value="0">
                                </td>
                            </tr>
                            <tr class="table-active">
                                <td class="text-end fw-bold">Grand Total:</td>
                                <td class="text-end">
                                    <strong><span id="grandTotalDisplay">$0.00</span></strong>
                                    <input type="hidden" name="grand_total" id="grandTotalInput" value="0">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.takeoffs.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Create Takeoff
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var container = $('#items-container');
    var itemIndex = 1;

    // Add Row
    $('#addRowBtn').on('click', function() {
        var firstRow = container.find('.item-row:first');
        var newRow = firstRow.clone();
        newRow.attr('data-index', itemIndex);

        // Update input/select names and clear values
        newRow.find('input, select').each(function() {
            var name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace(/items\[\d+\]/, 'items[' + itemIndex + ']'));
            }
            if ($(this).is('select')) {
                $(this).val('');
            } else if ($(this).hasClass('item-line-total')) {
                $(this).val('0.00');
            } else {
                $(this).val('');
            }
        });

        // Enable remove button on new row
        newRow.find('.remove-item-btn').prop('disabled', false);

        container.append(newRow);
        itemIndex++;
        updateRemoveButtons();
    });

    // Remove Row (event delegation)
    container.on('click', '.remove-item-btn', function() {
        if (!$(this).prop('disabled')) {
            $(this).closest('.item-row').remove();
            updateRemoveButtons();
            calculateGrandTotal();
        }
    });

    // Update remove buttons - disable if only one row
    function updateRemoveButtons() {
        var rows = container.find('.item-row');
        rows.find('.remove-item-btn').prop('disabled', rows.length <= 1);
    }

    // Calculate line total on qty/price change (event delegation)
    container.on('input', '.item-qty, .item-price', function() {
        var row = $(this).closest('.item-row');
        calculateLineTotal(row);
        calculateGrandTotal();
    });

    // Calculate line total for a single row
    function calculateLineTotal(row) {
        var qty = parseFloat(row.find('.item-qty').val()) || 0;
        var price = parseFloat(row.find('.item-price').val()) || 0;
        var lineTotal = qty * price;
        row.find('.item-line-total').val(lineTotal.toFixed(2));
    }

    // Calculate grand total from all line totals
    function calculateGrandTotal() {
        var grandTotal = 0;
        container.find('.item-line-total').each(function() {
            grandTotal += parseFloat($(this).val()) || 0;
        });

        $('#subtotalDisplay').text('$' + grandTotal.toFixed(2));
        $('#subtotalInput').val(grandTotal.toFixed(2));
        $('#grandTotalDisplay').text('$' + grandTotal.toFixed(2));
        $('#grandTotalInput').val(grandTotal.toFixed(2));
    }
});
</script>
@endpush
