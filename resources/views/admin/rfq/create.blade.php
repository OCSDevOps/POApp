@extends('admin.layouts.app')

@section('title', 'Create RFQ')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">New RFQ</h5>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.rfq.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-select" required>
                            <option value="">Select project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->proj_id }}">{{ $project->proj_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due date</label>
                        <input type="date" class="form-control" name="due_date" required>
                    </div>

                    <hr>
                    <h6>Suppliers</h6>
                    <div class="mb-3">
                        <select name="supplier_ids[]" class="form-select" multiple required>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->sup_id }}">{{ $supplier->sup_name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl (Cmd) to select multiple suppliers.</small>
                    </div>

                    <hr>
                    <h6>Items</h6>
                    <div id="items-container">
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
                                <label class="form-label">Target price</label>
                                <input type="number" step="0.01" name="items[0][target_price]" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm add-row">Add</button>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-3">
                        <button class="btn btn-primary" type="submit">Create RFQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (() => {
        const container = document.getElementById('items-container');
        let index = 1;
        container.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-row')) {
                const row = container.querySelector('.item-row').cloneNode(true);
                row.querySelectorAll('input, select').forEach(input => {
                    const name = input.getAttribute('name');
                    const newName = name.replace(/items\\[\\d+\\]/, `items[${index}]`);
                    input.setAttribute('name', newName);
                    input.value = '';
                });
                row.querySelector('.add-row').classList.replace('add-row', 'remove-row');
                row.querySelector('.remove-row').classList.replace('btn-outline-secondary', 'btn-outline-danger');
                row.querySelector('.remove-row').textContent = 'Remove';
                container.appendChild(row);
                index++;
            } else if (e.target.classList.contains('remove-row')) {
                e.target.closest('.item-row').remove();
            }
        });
    })();
</script>
@endpush
@endsection
