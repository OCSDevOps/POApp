@extends('layouts.admin')

@section('title', 'Units of Measure')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Units of Measure</h4>
                <p class="text-muted mb-0">Manage measurement units used on items, catalogs, and purchase orders.</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUomModal">
                <i class="fa fa-plus me-1"></i> New Unit
            </button>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">All Units</div>
            <div class="card-body table-responsive">
                <table class="table table-striped datatable align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $unit)
                            <tr>
                                <td>{{ $unit->uom_id }}</td>
                                <td>{{ $unit->uom_name }}</td>
                                <td>{{ $unit->uom_detail }}</td>
                                <td>
                                    @if($unit->uom_status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Locked</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary me-1 edit-btn"
                                            data-id="{{ $unit->uom_id }}"
                                            data-name="{{ $unit->uom_name }}"
                                            data-detail="{{ $unit->uom_detail }}"
                                            data-status="{{ $unit->uom_status }}">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <form action="{{ route('admin.uom.destroy', $unit) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this unit of measure?')">
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
<div class="modal fade" id="createUomModal" tabindex="-1" aria-labelledby="createUomLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.uom.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createUomLabel">New Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="uom_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="uom_detail" class="form-control" rows="3"></textarea>
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
<div class="modal fade" id="editUomModal" tabindex="-1" aria-labelledby="editUomLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editUomForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="uom_status" value="0">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUomLabel">Edit Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="uom_name" id="edit_uom_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="uom_detail" id="edit_uom_detail" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="edit_uom_status" name="uom_status_toggle" value="1">
                        <label class="form-check-label" for="edit_uom_status">Active</label>
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
            const isActive = $(this).data('status') == 1;

            $('#edit_uom_name').val($(this).data('name'));
            $('#edit_uom_detail').val($(this).data('detail'));
            $('#edit_uom_status').prop('checked', isActive);
            $('#editUomForm').find('input[name="uom_status"][type="hidden"]').val(isActive ? 1 : 0);

            const action = "{{ route('admin.uom.update', ':id') }}".replace(':id', id);
            $('#editUomForm').attr('action', action);

            $('#editUomModal').modal('show');
        });

        $('#edit_uom_status').on('change', function () {
            $('#editUomForm').find('input[name="uom_status"][type="hidden"]').val(this.checked ? 1 : 0);
        });
    });
</script>
@endpush
