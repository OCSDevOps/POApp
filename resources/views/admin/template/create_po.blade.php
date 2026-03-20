@extends('layouts.admin')

@section('title', 'Create PO from Template: ' . $template->pot_name)

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 font-weight-bold text-primary">
            <i class="fas fa-shopping-cart me-1"></i> Create PO from Template &mdash; {{ $template->pot_name }}
        </h5>
        <a href="{{ route('admin.template.show', $template->pot_id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Template
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

    <form method="POST" action="{{ route('admin.template.storepo', $template->pot_id) }}">
        @csrf

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-cog me-1"></i> Purchase Order Settings
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="project_id">Project <span class="text-danger">*</span></label>
                        <select class="form-select" id="project_id" name="project_id" required>
                            <option value="">-- Select Project --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->proj_id }}"
                                    @selected(old('project_id', $template->pot_default_project_id) == $project->proj_id)>
                                    {{ $project->proj_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="supplier_id">Supplier <span class="text-danger">*</span></label>
                        <select class="form-select" id="supplier_id" name="supplier_id" required>
                            <option value="">-- Select Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->sup_id }}"
                                    @selected(old('supplier_id', $template->pot_default_supplier_id) == $supplier->sup_id)>
                                    {{ $supplier->sup_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list me-1"></i> Items
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th class="text-end">Default Qty</th>
                                <th class="text-end">Override Qty <span class="text-danger">*</span></th>
                                <th>UOM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($template->items as $templateItem)
                                <tr>
                                    <td>
                                        <span class="text-muted">{{ $templateItem->item->item_code ?? '—' }}</span>
                                    </td>
                                    <td>{{ $templateItem->item->item_name ?? 'Item #' . $templateItem->poti_item_id }}</td>
                                    <td class="text-end text-muted">
                                        {{ $templateItem->poti_default_quantity ?? '—' }}
                                    </td>
                                    <td>
                                        <input type="number" class="form-control text-end"
                                               name="quantities[{{ $templateItem->poti_item_id }}]"
                                               value="{{ old('quantities.' . $templateItem->poti_item_id, $templateItem->poti_default_quantity) }}"
                                               min="0" step="0.01" required>
                                    </td>
                                    <td>{{ $templateItem->unitOfMeasure->uom_name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        No items in this template.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.template.show', $template->pot_id) }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-shopping-cart me-1"></i> Create Purchase Order
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-fill override qty from default when empty
    $('input[name^="quantities"]').each(function() {
        if (!$(this).val()) {
            const defaultQty = $(this).closest('tr').find('td:eq(2)').text().trim();
            if (defaultQty && defaultQty !== '—') {
                $(this).val(defaultQty);
            }
        }
    });
});
</script>
@endpush
