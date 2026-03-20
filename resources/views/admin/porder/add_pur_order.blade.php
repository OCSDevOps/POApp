@extends('layouts.admin')

@section('title', 'Create Purchase Order')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Create Purchase Order</h1>
    <a href="{{ route('admin.porder.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to List
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.porder.store') }}" id="poForm" enctype="multipart/form-data">
    @csrf

    {{-- Header Section --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-info-circle me-1"></i> PO Details
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="po_project" class="form-label">Project <span class="text-danger">*</span></label>
                    <select name="po_project" id="po_project" class="form-select" required>
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->proj_id }}" {{ old('po_project') == $project->proj_id ? 'selected' : '' }}>
                                {{ $project->proj_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="po_supplier" class="form-label">Supplier <span class="text-danger">*</span></label>
                    <select name="po_supplier" id="po_supplier" class="form-select" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->sup_id }}" {{ old('po_supplier') == $supplier->sup_id ? 'selected' : '' }}>
                                {{ $supplier->sup_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="po_address" class="form-label">Delivery Address <span class="text-danger">*</span></label>
                    <input type="text" name="po_address" id="po_address" class="form-control"
                           value="{{ old('po_address') }}" required>
                    <div id="delivery-address-display" class="mt-2" style="display: none;">
                        <small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i> Project Address:</small>
                        <p class="mb-0 small" id="delivery-address-text"></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="po_description" class="form-label">Description</label>
                    <textarea name="po_description" id="po_description" class="form-control" rows="3">{{ old('po_description') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label for="po_delivery_note" class="form-label">Delivery Note</label>
                    <textarea name="po_delivery_note" id="po_delivery_note" class="form-control" rows="3">{{ old('po_delivery_note') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Attachments --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-paperclip me-1"></i> Attachments
        </div>
        <div class="card-body">
            <label for="attachments" class="form-label">Upload Files</label>
            <input
                type="file"
                name="attachments[]"
                id="attachments"
                class="form-control"
                multiple
                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.csv,.txt"
            >
            <small class="text-muted">
                Up to 10 files. Maximum 10 MB per file.
            </small>
        </div>
    </div>

    {{-- Line Items Section --}}
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list me-1"></i> Line Items</span>
            <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                <i class="fas fa-plus me-1"></i> Add Item
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 12%;">Item Code</th>
                            <th style="width: 20%;">Item Name</th>
                            <th style="width: 10%;">Quantity</th>
                            <th style="width: 12%;">Unit Price</th>
                            <th style="width: 10%;">Tax Rate %</th>
                            <th style="width: 16%;">Cost Code</th>
                            <th style="width: 14%;">Line Total</th>
                            <th style="width: 6%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="items-container">
                        <tr class="item-row">
                            <td>
                                <input type="text" name="items[0][code]" class="form-control form-control-sm item-code"
                                       value="{{ old('items.0.code') }}" required>
                            </td>
                            <td>
                                <input type="text" name="items[0][name]" class="form-control form-control-sm item-name"
                                       value="{{ old('items.0.name') }}" required>
                            </td>
                            <td>
                                <input type="number" name="items[0][quantity]" class="form-control form-control-sm item-qty"
                                       min="1" step="1" value="{{ old('items.0.quantity', 1) }}" required>
                            </td>
                            <td>
                                <input type="number" name="items[0][price]" class="form-control form-control-sm item-price"
                                       min="0" step="0.01" value="{{ old('items.0.price', '0.00') }}" required>
                            </td>
                            <td>
                                <input type="number" name="items[0][tax_rate]" class="form-control form-control-sm item-tax"
                                       min="0" max="100" step="0.01" value="{{ old('items.0.tax_rate', '0') }}">
                            </td>
                            <td>
                                <select name="items[0][cost_code]" class="form-select form-select-sm item-costcode">
                                    <option value="">Select</option>
                                    @foreach($costCodes as $cc)
                                        <option value="{{ $cc->ccode_id }}">{{ $cc->ccode_code }} - {{ $cc->ccode_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm item-total" readonly value="0.00">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove" disabled>
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Totals Section --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-calculator me-1"></i> Totals
        </div>
        <div class="card-body">
            <div class="row justify-content-end">
                <div class="col-md-4">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-end"><strong>Subtotal:</strong></td>
                            <td class="text-end" style="width: 150px;">
                                <span id="subtotal-display">$0.00</span>
                                <input type="hidden" name="po_subtotal" id="po_subtotal" value="0">
                            </td>
                        </tr>
                        <tr>
                            <td class="text-end"><strong>Tax:</strong></td>
                            <td class="text-end">
                                <span id="tax-display">$0.00</span>
                                <input type="hidden" name="po_tax" id="po_tax" value="0">
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-end"><strong>Grand Total:</strong></td>
                            <td class="text-end">
                                <strong><span id="grandtotal-display">$0.00</span></strong>
                                <input type="hidden" name="po_grand_total" id="po_grand_total" value="0">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Budget Warning --}}
    <div class="alert alert-warning mb-4" id="budget-warning" style="display: none;">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Budget Warning:</strong>
        <span id="budget-warning-text"></span>
    </div>

    {{-- Submit --}}
    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('admin.porder.index') }}" class="btn btn-secondary me-2">Cancel</a>
        <button type="submit" class="btn btn-primary" id="submitBtn">
            <i class="fas fa-save me-1"></i> Create Purchase Order
        </button>
    </div>
</form>

@push('scripts')
<script>
    $(document).ready(function() {
        const container = document.getElementById('items-container');
        let rowIndex = 1;

        // Add Item row
        $('#addItemBtn').on('click', function() {
            const firstRow = container.querySelector('.item-row');
            const newRow = firstRow.cloneNode(true);

            newRow.querySelectorAll('input, select').forEach(function(input) {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/items\[\d+\]/, 'items[' + rowIndex + ']'));
                }
                if (input.classList.contains('item-total')) {
                    input.value = '0.00';
                } else if (input.classList.contains('item-qty')) {
                    input.value = '1';
                } else if (input.classList.contains('item-price')) {
                    input.value = '0.00';
                } else if (input.classList.contains('item-tax')) {
                    input.value = '0';
                } else if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else {
                    input.value = '';
                }
            });

            const removeBtn = newRow.querySelector('.remove-row');
            removeBtn.disabled = false;

            container.appendChild(newRow);
            rowIndex++;
            updateTotals();
        });

        // Remove Item row
        $(container).on('click', '.remove-row', function() {
            const rows = container.querySelectorAll('.item-row');
            if (rows.length > 1) {
                $(this).closest('.item-row').remove();
                updateTotals();
            }
        });

        // Calculate line totals on input change
        $(container).on('input', '.item-qty, .item-price, .item-tax', function() {
            const row = $(this).closest('.item-row');
            calculateLineTotal(row);
            updateTotals();
        });

        function calculateLineTotal(row) {
            const qty = parseFloat(row.find('.item-qty').val()) || 0;
            const price = parseFloat(row.find('.item-price').val()) || 0;
            const taxRate = parseFloat(row.find('.item-tax').val()) || 0;
            const subtotal = qty * price;
            const tax = subtotal * (taxRate / 100);
            const lineTotal = subtotal + tax;
            row.find('.item-total').val(lineTotal.toFixed(2));
        }

        function updateTotals() {
            let subtotal = 0;
            let totalTax = 0;

            $('#items-container .item-row').each(function() {
                const qty = parseFloat($(this).find('.item-qty').val()) || 0;
                const price = parseFloat($(this).find('.item-price').val()) || 0;
                const taxRate = parseFloat($(this).find('.item-tax').val()) || 0;
                const lineSubtotal = qty * price;
                const lineTax = lineSubtotal * (taxRate / 100);
                subtotal += lineSubtotal;
                totalTax += lineTax;
            });

            const grandTotal = subtotal + totalTax;

            $('#subtotal-display').text('$' + subtotal.toFixed(2));
            $('#tax-display').text('$' + totalTax.toFixed(2));
            $('#grandtotal-display').text('$' + grandTotal.toFixed(2));
            $('#po_subtotal').val(subtotal.toFixed(2));
            $('#po_tax').val(totalTax.toFixed(2));
            $('#po_grand_total').val(grandTotal.toFixed(2));
        }

        // Project change - fetch delivery address
        $('#po_project').on('change', function() {
            const projectId = $(this).val();
            if (projectId) {
                $.ajax({
                    url: '{{ route("admin.porder.projectaddress") }}',
                    type: 'GET',
                    data: { project_id: projectId },
                    success: function(response) {
                        if (response.address) {
                            $('#delivery-address-text').text(response.address);
                            $('#delivery-address-display').show();
                        } else {
                            $('#delivery-address-display').hide();
                        }
                    },
                    error: function() {
                        $('#delivery-address-display').hide();
                    }
                });
            } else {
                $('#delivery-address-display').hide();
            }
        });

        // Budget check before submit
        $('#poForm').on('submit', function(e) {
            const projectId = $('#po_project').val();
            const grandTotal = parseFloat($('#po_grand_total').val()) || 0;

            if (projectId && grandTotal > 0) {
                // Collect cost code totals for budget check
                const costCodeTotals = {};
                $('#items-container .item-row').each(function() {
                    const costCode = $(this).find('.item-costcode').val();
                    const qty = parseFloat($(this).find('.item-qty').val()) || 0;
                    const price = parseFloat($(this).find('.item-price').val()) || 0;
                    if (costCode) {
                        costCodeTotals[costCode] = (costCodeTotals[costCode] || 0) + (qty * price);
                    }
                });
            }
        });

        // Initial calculation
        updateTotals();
    });
</script>
@endpush
@endsection
