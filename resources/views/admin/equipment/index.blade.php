@extends('layouts.admin')

@section('title', 'Equipment')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Equipment</h4>
                <p class="text-muted mb-0">Manage equipment assets with status and usage tracking.</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEquipmentModal">
                <i class="fa fa-plus me-1"></i> New Equipment
            </button>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">All Equipment</div>
            <div class="card-body table-responsive">
                <table class="table table-striped datatable align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Tag</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipments as $eq)
                            <tr>
                                <td>{{ $eq->eq_id }}</td>
                                <td>{{ $eq->eqm_asset_name }}</td>
                                <td>{{ $eq->eqm_asset_tag }}</td>
                                <td>{{ $eq->eqm_asset_type }}</td>
                                <td>{{ $eq->eqm_category }}</td>
                                <td>{{ $eq->eqm_status }}</td>
                                <td>{{ $eq->eqm_location }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary me-1 edit-btn"
                                            data-id="{{ $eq->eq_id }}"
                                            data-name="{{ $eq->eqm_asset_name }}"
                                            data-desc="{{ $eq->eqm_asset_description }}"
                                            data-type="{{ $eq->eqm_asset_type }}"
                                            data-tag="{{ $eq->eqm_asset_tag }}"
                                            data-category="{{ $eq->eqm_category }}"
                                            data-status="{{ $eq->eqm_status }}"
                                            data-location="{{ $eq->eqm_location }}"
                                            data-reading="{{ $eq->eqm_existing_reading }}"
                                            data-estimate="{{ $eq->eqm_estimate_usage }}"
                                            data-operator="{{ $eq->eqm_current_operator }}"
                                            data-supplier="{{ $eq->eqm_supplier }}"
                                            data-license="{{ $eq->eqm_license_plate }}"
                                            data-year="{{ $eq->eqm_year }}"
                                            data-brand="{{ $eq->eqm_brand }}"
                                            data-model="{{ $eq->eqm_model }}">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <form action="{{ route('admin.equipment.destroy', $eq) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this equipment?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createEquipmentModal" tabindex="-1" aria-labelledby="createEquipmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.equipment.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createEquipmentLabel">New Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="eqm_asset_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tag</label>
                            <input type="text" name="eqm_asset_tag" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <input type="text" name="eqm_asset_type" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <input type="text" name="eqm_category" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="eqm_asset_description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <input type="text" name="eqm_status" class="form-control" value="Available">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="eqm_location" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Existing Reading</label>
                            <input type="number" step="0.01" name="eqm_existing_reading" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estimate Usage</label>
                            <input type="number" step="0.01" name="eqm_estimate_usage" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">License Plate</label>
                            <input type="text" name="eqm_license_plate" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year</label>
                            <input type="text" name="eqm_year" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Brand</label>
                            <input type="text" name="eqm_brand" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Model</label>
                            <input type="text" name="eqm_model" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Operator (user id)</label>
                            <input type="number" name="eqm_current_operator" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier (id)</label>
                            <input type="number" name="eqm_supplier" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Picture</label>
                            <input type="file" name="eqm_asset_picture" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editEquipmentModal" tabindex="-1" aria-labelledby="editEquipmentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editEquipmentForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editEquipmentLabel">Edit Equipment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="eqm_asset_name" id="edit_eq_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tag</label>
                            <input type="text" name="eqm_asset_tag" id="edit_eq_tag" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <input type="text" name="eqm_asset_type" id="edit_eq_type" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <input type="text" name="eqm_category" id="edit_eq_category" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="eqm_asset_description" id="edit_eq_desc" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <input type="text" name="eqm_status" id="edit_eq_status" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="eqm_location" id="edit_eq_location" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Existing Reading</label>
                            <input type="number" step="0.01" name="eqm_existing_reading" id="edit_eq_reading" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Estimate Usage</label>
                            <input type="number" step="0.01" name="eqm_estimate_usage" id="edit_eq_estimate" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">License Plate</label>
                            <input type="text" name="eqm_license_plate" id="edit_eq_license" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Year</label>
                            <input type="text" name="eqm_year" id="edit_eq_year" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Brand</label>
                            <input type="text" name="eqm_brand" id="edit_eq_brand" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Model</label>
                            <input type="text" name="eqm_model" id="edit_eq_model" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Operator (user id)</label>
                            <input type="number" name="eqm_current_operator" id="edit_eq_operator" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier (id)</label>
                            <input type="number" name="eqm_supplier" id="edit_eq_supplier" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Picture</label>
                            <input type="file" name="eqm_asset_picture" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('.edit-btn').on('click', function () {
            const id = $(this).data('id');
            $('#edit_eq_name').val($(this).data('name'));
            $('#edit_eq_desc').val($(this).data('desc'));
            $('#edit_eq_type').val($(this).data('type'));
            $('#edit_eq_tag').val($(this).data('tag'));
            $('#edit_eq_category').val($(this).data('category'));
            $('#edit_eq_status').val($(this).data('status'));
            $('#edit_eq_location').val($(this).data('location'));
            $('#edit_eq_reading').val($(this).data('reading'));
            $('#edit_eq_estimate').val($(this).data('estimate'));
            $('#edit_eq_operator').val($(this).data('operator'));
            $('#edit_eq_supplier').val($(this).data('supplier'));
            $('#edit_eq_license').val($(this).data('license'));
            $('#edit_eq_year').val($(this).data('year'));
            $('#edit_eq_brand').val($(this).data('brand'));
            $('#edit_eq_model').val($(this).data('model'));

            const action = "{{ route('admin.equipment.update', ':id') }}".replace(':id', id);
            $('#editEquipmentForm').attr('action', action);

            $('#editEquipmentModal').modal('show');
        });
    });
</script>
@endpush
