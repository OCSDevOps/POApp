@extends('layouts.admin')

@section('title', 'Create PO Template')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-plus-circle me-1"></i> Create PO Template
        </h6>
        <a href="{{ route('admin.template.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Templates
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

    <form method="POST" action="{{ route('admin.template.store') }}">
        @csrf

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Template Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="pot_name">Template Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pot_name" name="pot_name" value="{{ old('pot_name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="pot_description">Description</label>
                        <textarea class="form-control" id="pot_description" name="pot_description" rows="3">{{ old('pot_description') }}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="pot_default_project_id">Default Project</label>
                        <select class="form-select" id="pot_default_project_id" name="pot_default_project_id">
                            <option value="">-- Select Project --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->proj_id }}" @selected(old('pot_default_project_id') == $project->proj_id)>
                                    {{ $project->proj_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="pot_default_supplier_id">Default Supplier</label>
                        <select class="form-select" id="pot_default_supplier_id" name="pot_default_supplier_id">
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->sup_id }}" @selected(old('pot_default_supplier_id') == $supplier->sup_id)>
                                    {{ $supplier->sup_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list me-1"></i> Template Items
                </h6>
                <button type="button" class="btn btn-success btn-sm" id="addItemBtn">
                    <i class="fas fa-plus me-1"></i> Add Item
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Item <span class="text-danger">*</span></th>
                                <th style="width: 15%;">Default Quantity</th>
                                <th style="width: 20%;">UOM</th>
                                <th style="width: 25%;">Notes</th>
                                <th style="width: 10%;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            <tr class="item-row" data-index="0">
                                <td>
                                    <select name="items[0][item_id]" class="form-select" required>
                                        <option value="">-- Select Item --</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->item_id }}">{{ $item->item_code }} - {{ $item->item_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][default_quantity]" class="form-control" min="0" step="0.01">
                                </td>
                                <td>
                                    <select name="items[0][uom_id]" class="form-select">
                                        <option value="">-- Select UOM --</option>
                                        @foreach($uoms as $uom)
                                            <option value="{{ $uom->uom_id }}">{{ $uom->uom_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="items[0][notes]" class="form-control" placeholder="Optional notes">
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
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.template.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Create Template
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
(() => {
    const container = document.getElementById('items-container');
    const addBtn = document.getElementById('addItemBtn');
    let index = 1;

    addBtn.addEventListener('click', () => {
        const firstRow = container.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);
        newRow.setAttribute('data-index', index);

        newRow.querySelectorAll('input, select').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/items\[\d+\]/, `items[${index}]`));
            }
            input.value = '';
        });

        const removeBtn = newRow.querySelector('.remove-item-btn');
        removeBtn.disabled = false;

        container.appendChild(newRow);
        index++;
        updateRemoveButtons();
    });

    container.addEventListener('click', (e) => {
        const btn = e.target.closest('.remove-item-btn');
        if (btn && !btn.disabled) {
            btn.closest('.item-row').remove();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const rows = container.querySelectorAll('.item-row');
        rows.forEach((row, i) => {
            const btn = row.querySelector('.remove-item-btn');
            btn.disabled = rows.length <= 1;
        });
    }
})();
</script>
@endpush
