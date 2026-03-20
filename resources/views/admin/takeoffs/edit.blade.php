@extends('layouts.admin')

@section('title', 'Edit Takeoff - ' . $takeoff->to_number)

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-edit me-1"></i> Edit Takeoff: {{ $takeoff->to_number }} — {{ $takeoff->to_title }}
        </h6>
        <a href="{{ route('admin.takeoffs.show', $takeoff->to_id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Takeoff
        </a>
    </div>

    @include('partials.validation-errors')

    <form method="POST" action="{{ route('admin.takeoffs.update', $takeoff->to_id) }}" id="takeoffEditForm">
        @csrf
        @method('PUT')

        {{-- Card 1: Takeoff Information --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-1"></i> Takeoff Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="project_id">Project <span class="text-danger">*</span></label>
                        <select class="form-select" id="project_id" name="project_id" required>
                            <option value="">-- Select Project --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->proj_id }}"
                                    @selected(old('project_id', $takeoff->to_project_id) == $project->proj_id)>
                                    {{ $project->proj_name }} ({{ $project->proj_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title"
                               value="{{ old('title', $takeoff->to_title) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small d-block">TO Number</label>
                        <p class="mb-0 fw-semibold mt-2">{{ $takeoff->to_number }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  rows="3">{{ old('description', $takeoff->to_description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Drawings --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-drafting-compass me-1"></i> Drawings
                </h6>
            </div>
            <div class="card-body">
                @if($takeoff->activeDrawings->count() > 0)
                    <div class="mb-3">
                        <label class="form-label mb-2">Existing Drawings</label>
                        @foreach($takeoff->activeDrawings as $drawing)
                            <div class="d-flex align-items-center justify-content-between border rounded px-3 py-2 mb-2">
                                <div>
                                    @if(Str::endsWith(strtolower($drawing->tdr_original_name), '.pdf'))
                                        <i class="fas fa-file-pdf me-1 text-danger"></i>
                                    @else
                                        <i class="fas fa-file-image me-1 text-primary"></i>
                                    @endif
                                    <span class="fw-semibold">{{ $drawing->tdr_original_name }}</span>
                                    <small class="text-muted ms-2">({{ $drawing->file_size_formatted }})</small>
                                    <span class="badge bg-{{ $drawing->ai_status_badge }} ms-2">{{ ucfirst($drawing->tdr_ai_status) }}</span>
                                </div>
                                <form method="POST"
                                      action="{{ route('admin.takeoffs.delete-drawing', [$takeoff->to_id, $drawing->tdr_id]) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this drawing?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash me-1"></i> Remove
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-3">No drawings uploaded yet.</p>
                @endif

                <hr>
                <label class="form-label">Upload More Drawings</label>
            </div>
        </div>

        {{-- Separate Upload Form (outside main edit form) --}}
    </form>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-upload me-1"></i> Upload Additional Drawings
            </h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.takeoffs.upload-drawings', $takeoff->to_id) }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-8 mb-3">
                        <label for="drawings" class="form-label">Select Files</label>
                        <input type="file" name="drawings[]" id="drawings" class="form-control"
                               multiple accept=".pdf,.jpg,.jpeg,.png,.tiff,.tif,.dwg">
                        <small class="text-muted">Accepted formats: PDF, JPG, PNG, TIFF, DWG</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-upload me-1"></i> Upload Drawings
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Card 3: Line Items (re-open main form scope via a second form) --}}
    <form method="POST" action="{{ route('admin.takeoffs.update', $takeoff->to_id) }}" id="takeoffItemsForm">
        @csrf
        @method('PUT')

        {{-- Hidden duplicates of the info fields so the full payload is submitted --}}
        <input type="hidden" name="project_id" id="hidden_project_id" value="{{ old('project_id', $takeoff->to_project_id) }}">
        <input type="hidden" name="title" id="hidden_title" value="{{ old('title', $takeoff->to_title) }}">
        <input type="hidden" name="description" id="hidden_description" value="{{ old('description', $takeoff->to_description) }}">

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list me-1"></i> Line Items
                </h6>
                <button type="button" class="btn btn-success btn-sm" id="addItemBtn">
                    <i class="fas fa-plus me-1"></i> Add Item
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 12%;">Item Code</th>
                                <th style="width: 20%;">Description <span class="text-danger">*</span></th>
                                <th style="width: 8%;">Qty <span class="text-danger">*</span></th>
                                <th style="width: 12%;">UOM</th>
                                <th style="width: 12%;">Unit Price <span class="text-danger">*</span></th>
                                <th style="width: 14%;">Cost Code</th>
                                <th style="width: 10%;">Subtotal</th>
                                <th style="width: 6%;">Source</th>
                                <th style="width: 6%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            @forelse($takeoff->activeItems as $i => $item)
                                <tr class="item-row" data-index="{{ $i }}">
                                    <td>
                                        <input type="text" name="items[{{ $i }}][item_code]"
                                               class="form-control form-control-sm"
                                               value="{{ old('items.'.$i.'.item_code', $item->tod_item_code) }}"
                                               placeholder="Item code">
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $i }}][description]"
                                               class="form-control form-control-sm"
                                               value="{{ old('items.'.$i.'.description', $item->tod_description) }}"
                                               required placeholder="Description">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][quantity]"
                                               class="form-control form-control-sm item-qty"
                                               min="0" step="0.01"
                                               value="{{ old('items.'.$i.'.quantity', $item->tod_quantity) }}" required>
                                    </td>
                                    <td>
                                        <select name="items[{{ $i }}][uom_id]" class="form-select form-select-sm">
                                            <option value="">-- UOM --</option>
                                            @foreach($uoms as $uom)
                                                <option value="{{ $uom->uom_id }}"
                                                    @selected(old('items.'.$i.'.uom_id', $item->tod_uom_id) == $uom->uom_id)>
                                                    {{ $uom->uom_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $i }}][unit_price]"
                                               class="form-control form-control-sm item-price"
                                               min="0" step="0.01"
                                               value="{{ old('items.'.$i.'.unit_price', $item->tod_unit_price) }}" required>
                                    </td>
                                    <td>
                                        <select name="items[{{ $i }}][cost_code_id]" class="form-select form-select-sm">
                                            <option value="">-- Cost Code --</option>
                                            @foreach($costCodes as $cc)
                                                <option value="{{ $cc->cc_id }}"
                                                    @selected(old('items.'.$i.'.cost_code_id', $item->tod_cost_code_id) == $cc->cc_id)>
                                                    {{ $cc->cc_no }} - {{ $cc->cc_description }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm item-subtotal" readonly
                                               value="{{ number_format($item->tod_quantity * $item->tod_unit_price, 2) }}">
                                    </td>
                                    <td class="text-center">
                                        @if($item->tod_source === 'ai')
                                            <span class="badge bg-info" title="AI-sourced ({{ $item->tod_ai_confidence }}% confidence)">
                                                <i class="fas fa-robot"></i>
                                            </span>
                                        @else
                                            <span class="badge bg-secondary" title="Manual entry">
                                                <i class="fas fa-pencil-alt"></i>
                                            </span>
                                        @endif
                                        <input type="hidden" name="items[{{ $i }}][source]" value="{{ $item->tod_source }}">
                                        <input type="hidden" name="items[{{ $i }}][ai_confidence]" value="{{ $item->tod_ai_confidence }}">
                                        <input type="hidden" name="items[{{ $i }}][notes]" value="{{ $item->tod_notes }}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"
                                                title="Remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="item-row" data-index="0">
                                    <td>
                                        <input type="text" name="items[0][item_code]"
                                               class="form-control form-control-sm" placeholder="Item code">
                                    </td>
                                    <td>
                                        <input type="text" name="items[0][description]"
                                               class="form-control form-control-sm" required placeholder="Description">
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][quantity]"
                                               class="form-control form-control-sm item-qty"
                                               min="0" step="0.01" value="1" required>
                                    </td>
                                    <td>
                                        <select name="items[0][uom_id]" class="form-select form-select-sm">
                                            <option value="">-- UOM --</option>
                                            @foreach($uoms as $uom)
                                                <option value="{{ $uom->uom_id }}">{{ $uom->uom_name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="items[0][unit_price]"
                                               class="form-control form-control-sm item-price"
                                               min="0" step="0.01" value="0.00" required>
                                    </td>
                                    <td>
                                        <select name="items[0][cost_code_id]" class="form-select form-select-sm">
                                            <option value="">-- Cost Code --</option>
                                            @foreach($costCodes as $cc)
                                                <option value="{{ $cc->cc_id }}">{{ $cc->cc_no }} - {{ $cc->cc_description }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm item-subtotal" readonly value="0.00">
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary" title="Manual entry">
                                            <i class="fas fa-pencil-alt"></i>
                                        </span>
                                        <input type="hidden" name="items[0][source]" value="manual">
                                        <input type="hidden" name="items[0][ai_confidence]" value="">
                                        <input type="hidden" name="items[0][notes]" value="">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"
                                                title="Remove" disabled>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Totals Section --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calculator me-1"></i> Totals
                </h6>
            </div>
            <div class="card-body">
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end" style="width: 150px;">
                                    <span id="subtotal-display">$0.00</span>
                                    <input type="hidden" name="subtotal" id="subtotal" value="0">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-end"><strong>Tax:</strong></td>
                                <td class="text-end">
                                    <span id="tax-display">$0.00</span>
                                    <input type="hidden" name="tax" id="tax" value="0">
                                </td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-end"><strong>Grand Total:</strong></td>
                                <td class="text-end">
                                    <strong><span id="grandtotal-display">$0.00</span></strong>
                                    <input type="hidden" name="grand_total" id="grand_total" value="0">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.takeoffs.show', $takeoff->to_id) }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save me-1"></i> Update Takeoff
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const container = document.getElementById('items-container');
    let rowIndex = {{ $takeoff->activeItems->count() > 0 ? $takeoff->activeItems->count() : 1 }};

    // Sync top form fields to hidden fields on the items form
    function syncHiddenFields() {
        $('#hidden_project_id').val($('#project_id').val());
        $('#hidden_title').val($('#title').val());
        $('#hidden_description').val($('#description').val());
    }

    $('#project_id, #title, #description').on('change input', syncHiddenFields);

    // Add Item row
    $('#addItemBtn').on('click', function() {
        const firstRow = container.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);
        newRow.setAttribute('data-index', rowIndex);

        newRow.querySelectorAll('input, select').forEach(function(input) {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/items\[\d+\]/, 'items[' + rowIndex + ']'));
            }
            if (input.classList.contains('item-subtotal')) {
                input.value = '0.00';
            } else if (input.classList.contains('item-qty')) {
                input.value = '1';
            } else if (input.classList.contains('item-price')) {
                input.value = '0.00';
            } else if (input.type === 'hidden' && name && name.includes('[source]')) {
                input.value = 'manual';
            } else if (input.type === 'hidden') {
                input.value = '';
            } else if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            } else {
                input.value = '';
            }
        });

        // Reset source badge to manual
        const sourceCell = newRow.querySelectorAll('td')[7];
        if (sourceCell) {
            const badge = sourceCell.querySelector('.badge');
            if (badge) {
                badge.className = 'badge bg-secondary';
                badge.setAttribute('title', 'Manual entry');
                badge.innerHTML = '<i class="fas fa-pencil-alt"></i>';
            }
        }

        const removeBtn = newRow.querySelector('.remove-item-btn');
        removeBtn.disabled = false;

        container.appendChild(newRow);
        rowIndex++;
        updateRemoveButtons();
        updateTotals();
    });

    // Remove Item row
    $(container).on('click', '.remove-item-btn', function() {
        if (!$(this).prop('disabled')) {
            $(this).closest('.item-row').remove();
            updateRemoveButtons();
            updateTotals();
        }
    });

    // Calculate line subtotals on input change
    $(container).on('input', '.item-qty, .item-price', function() {
        const row = $(this).closest('.item-row');
        calculateLineSubtotal(row);
        updateTotals();
    });

    function calculateLineSubtotal(row) {
        const qty = parseFloat(row.find('.item-qty').val()) || 0;
        const price = parseFloat(row.find('.item-price').val()) || 0;
        const subtotal = qty * price;
        row.find('.item-subtotal').val(subtotal.toFixed(2));
    }

    function updateTotals() {
        let subtotal = 0;

        $('#items-container .item-row').each(function() {
            const qty = parseFloat($(this).find('.item-qty').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            subtotal += qty * price;
        });

        const tax = 0; // Tax calculated server-side or adjustable if needed
        const grandTotal = subtotal + tax;

        $('#subtotal-display').text('$' + subtotal.toFixed(2));
        $('#tax-display').text('$' + tax.toFixed(2));
        $('#grandtotal-display').text('$' + grandTotal.toFixed(2));
        $('#subtotal').val(subtotal.toFixed(2));
        $('#tax').val(tax.toFixed(2));
        $('#grand_total').val(grandTotal.toFixed(2));
    }

    function updateRemoveButtons() {
        const rows = container.querySelectorAll('.item-row');
        rows.forEach(function(row) {
            const btn = row.querySelector('.remove-item-btn');
            btn.disabled = rows.length <= 1;
        });
    }

    // Recalculate all line subtotals on page load
    $('#items-container .item-row').each(function() {
        calculateLineSubtotal($(this));
    });
    updateTotals();
    updateRemoveButtons();

    // Sync hidden fields on submit
    $('#takeoffItemsForm').on('submit', function() {
        syncHiddenFields();
    });
});
</script>
@endpush
