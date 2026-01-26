@extends('layouts.admin')

@section('title','New Checklist')

@section('content')
<h4 class="mb-3">New Checklist</h4>
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.checklists.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="cl_name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Frequency</label>
                    <input type="text" name="cl_frequency" class="form-control" placeholder="Weekly / Monthly">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="cl_start_date" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Equipments</label>
                    <select name="cl_eq_ids[]" class="form-select" multiple>
                        @foreach($equipments as $eq)
                            <option value="{{ $eq->eq_id }}">{{ $eq->eqm_asset_name ?? ('#'.$eq->eq_id) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Assign To Users</label>
                    <select name="cl_user_ids[]" class="form-select" multiple>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name ?? $user->email }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr class="my-4">
            <h6>Checklist Items</h6>
            <div id="items-wrapper">
                <div class="row g-2 mb-2 item-row">
                    <div class="col-11">
                        <input type="text" name="items[]" class="form-control" placeholder="Describe check step" required>
                    </div>
                    <div class="col-1 d-grid">
                        <button type="button" class="btn btn-outline-danger remove-item" aria-label="Remove item">&times;</button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-outline-secondary mb-3" id="add-item"><i class="fa fa-plus me-1"></i>Add item</button>

            <div class="mt-3">
                <button class="btn btn-primary" type="submit">Save</button>
                <a class="btn btn-light" href="{{ route('admin.checklists.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('add-item').addEventListener('click', function () {
        const wrapper = document.getElementById('items-wrapper');
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 item-row';
        row.innerHTML = `
            <div class="col-11">
                <input type="text" name="items[]" class="form-control" placeholder="Describe check step" required>
            </div>
            <div class="col-1 d-grid">
                <button type="button" class="btn btn-outline-danger remove-item" aria-label="Remove item">&times;</button>
            </div>`;
        wrapper.appendChild(row);
    });

    document.getElementById('items-wrapper').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item')) {
            const row = e.target.closest('.item-row');
            if (row && document.querySelectorAll('#items-wrapper .item-row').length > 1) {
                row.remove();
            }
        }
    });
</script>
@endpush
@endsection
