@extends('layouts.admin')

@section('title','Record Checklist')

@section('content')
<h4 class="mb-3">Record Checklist</h4>
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.performchecklists.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Checklist</label>
                    <select name="cl_id" id="cl_id" class="form-select" required>
                        <option value="">Select checklist</option>
                        @foreach($checklists as $checklist)
                            <option value="{{ $checklist->cl_id }}" data-items='@json($checklist->items)'>{{ $checklist->cl_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Equipment</label>
                    <select name="cl_eq_id" class="form-select">
                        <option value="">None</option>
                        @foreach($equipments as $eq)
                            <option value="{{ $eq->eq_id }}">{{ $eq->eqm_asset_name ?? ('#'.$eq->eq_id) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="cl_p_date" class="form-control" value="{{ now()->toDateString() }}" required>
                </div>
            </div>

            <hr class="my-4">
            <h6>Item Responses</h6>
            <div id="items-wrapper"></div>

            <div class="mt-3">
                <button class="btn btn-primary" type="submit">Save</button>
                <a class="btn btn-light" href="{{ route('admin.performchecklists.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const wrapper = document.getElementById('items-wrapper');
    document.getElementById('cl_id').addEventListener('change', function () {
        wrapper.innerHTML = '';
        const selected = this.options[this.selectedIndex];
        if (!selected || !selected.dataset.items) return;
        const items = JSON.parse(selected.dataset.items);
        items.forEach((item, idx) => {
            const row = document.createElement('div');
            row.className = 'row g-2 mb-3 border-bottom pb-2';
            row.innerHTML = `
                <input type="hidden" name="items[${idx}][cli_id]" value="${item.cli_id}">
                <div class="col-md-6">
                    <label class="form-label">${item.cli_item}</label>
                    <input type="text" name="items[${idx}][value]" class="form-control" placeholder="Result/Reading">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Notes</label>
                    <input type="text" name="items[${idx}][notes]" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Attachment</label>
                    <input type="file" name="items[${idx}][attachment]" class="form-control">
                </div>`;
            wrapper.appendChild(row);
        });
    });
</script>
@endpush
@endsection
