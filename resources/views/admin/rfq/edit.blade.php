@extends('layouts.admin')

@section('title', 'Edit RFQ')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit RFQ</h5>
                    <a href="{{ route('admin.rfq.show', $rfq->rfq_id) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.validation-errors')

                    <form method="POST" action="{{ route('admin.rfq.update', $rfq->rfq_id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Project <span class="text-danger">*</span></label>
                            <select name="project_id" class="form-select" required>
                                @foreach($projects as $project)
                                    <option value="{{ $project->proj_id }}" @selected(old('project_id', $rfq->rfq_project_id) == $project->proj_id)>
                                        {{ $project->proj_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   name="title" value="{{ old('title', $rfq->rfq_title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $rfq->rfq_description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Due Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                   name="due_date" value="{{ old('due_date', optional($rfq->rfq_due_date)->format('Y-m-d')) }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>
                        <h6><i class="fas fa-truck me-1"></i> Suppliers</h6>
                        <div class="mb-3">
                            @php
                                $selectedSupplierIds = old('supplier_ids', $rfq->suppliers->pluck('rfqs_supplier_id')->toArray());
                            @endphp
                            <select name="supplier_ids[]" class="form-select" multiple required size="5">
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->sup_id }}"
                                        @if(in_array($supplier->sup_id, $selectedSupplierIds)) selected @endif>
                                        {{ $supplier->sup_name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl (Cmd) to select multiple suppliers.</small>
                        </div>

                        <hr>
                        <h6><i class="fas fa-boxes me-1"></i> Items</h6>
                        <div id="items-container">
                            @php
                                $rfqItems = old('items', $rfq->items->map(function($item) {
                                    return [
                                        'item_id' => $item->rfqi_item_id,
                                        'quantity' => $item->rfqi_quantity,
                                        'uom_id' => $item->rfqi_uom_id,
                                        'target_price' => $item->rfqi_target_price,
                                    ];
                                })->toArray());
                            @endphp

                            @foreach($rfqItems as $idx => $rfqItem)
                                <div class="row g-2 align-items-end mb-2 item-row">
                                    <div class="col-md-4">
                                        @if($loop->first)
                                            <label class="form-label">Item</label>
                                        @endif
                                        <select name="items[{{ $idx }}][item_id]" class="form-select" required>
                                            <option value="">Select</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->item_id }}"
                                                    @if(($rfqItem['item_id'] ?? '') == $item->item_id) selected @endif>
                                                    {{ $item->item_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        @if($loop->first)
                                            <label class="form-label">Qty</label>
                                        @endif
                                        <input type="number" name="items[{{ $idx }}][quantity]" class="form-control"
                                               min="1" value="{{ $rfqItem['quantity'] ?? '' }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        @if($loop->first)
                                            <label class="form-label">UOM</label>
                                        @endif
                                        <select name="items[{{ $idx }}][uom_id]" class="form-select" required>
                                            @foreach($uoms as $uom)
                                                <option value="{{ $uom->uom_id }}"
                                                    @if(($rfqItem['uom_id'] ?? '') == $uom->uom_id) selected @endif>
                                                    {{ $uom->uom_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        @if($loop->first)
                                            <label class="form-label">Target $</label>
                                        @endif
                                        <input type="number" step="0.01" name="items[{{ $idx }}][target_price]"
                                               class="form-control" value="{{ $rfqItem['target_price'] ?? '' }}">
                                    </div>
                                    <div class="col-md-2">
                                        @if($loop->first)
                                            <button type="button" class="btn btn-outline-secondary btn-sm add-row">
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                                                <i class="fas fa-times"></i> Remove
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            @if(count($rfqItems) === 0)
                                <div class="row g-2 align-items-end mb-2 item-row">
                                    <div class="col-md-4">
                                        <label class="form-label">Item</label>
                                        <select name="items[0][item_id]" class="form-select" required>
                                            <option value="">Select</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->item_id }}">{{ $item->item_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Qty</label>
                                        <input type="number" name="items[0][quantity]" class="form-control" min="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">UOM</label>
                                        <select name="items[0][uom_id]" class="form-select" required>
                                            @foreach($uoms as $uom)
                                                <option value="{{ $uom->uom_id }}">{{ $uom->uom_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Target $</label>
                                        <input type="number" step="0.01" name="items[0][target_price]" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-secondary btn-sm add-row">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="d-grid mt-4">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-save me-1"></i> Update RFQ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const container = document.getElementById('items-container');
    let index = {{ count($rfqItems) ?: 1 }};
    container.addEventListener('click', (e) => {
        if (e.target.closest('.add-row')) {
            const row = container.querySelector('.item-row').cloneNode(true);
            row.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    const newName = name.replace(/items\[\d+\]/, `items[${index}]`);
                    input.setAttribute('name', newName);
                    if (input.tagName === 'SELECT') {
                        input.selectedIndex = 0;
                    } else {
                        input.value = '';
                    }
                }
            });
            row.querySelectorAll('label').forEach(l => l.remove());
            const btn = row.querySelector('.add-row');
            if (btn) {
                btn.className = 'btn btn-outline-danger btn-sm remove-row';
                btn.innerHTML = '<i class="fas fa-times"></i> Remove';
            }
            container.appendChild(row);
            index++;
        } else if (e.target.closest('.remove-row')) {
            e.target.closest('.item-row').remove();
        }
    });
})();
</script>
@endpush
