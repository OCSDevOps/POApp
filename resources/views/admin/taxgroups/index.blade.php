@extends('layouts.admin')

@section('title', 'Tax Groups')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Tax Groups</h4>
                <p class="text-muted mb-0">Manage tax groups and percentages applied to RFQs and purchase orders.</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaxGroupModal">
                <i class="fa fa-plus me-1"></i> New Tax Group
            </button>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">All Tax Groups</div>
            <div class="card-body table-responsive">
                <table class="table table-striped datatable align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Percentage</th>
                            <th>Description</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($taxGroups as $tax)
                            <tr>
                                <td>{{ $tax->id }}</td>
                                <td>{{ $tax->name }}</td>
                                <td>{{ $tax->percentage }}%</td>
                                <td>{{ $tax->description }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary me-1 edit-btn"
                                            data-id="{{ $tax->id }}"
                                            data-name="{{ $tax->name }}"
                                            data-percentage="{{ $tax->percentage }}"
                                            data-description="{{ $tax->description }}">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <form action="{{ route('admin.taxgroups.destroy', $tax) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this tax group?')">
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
<div class="modal fade" id="createTaxGroupModal" tabindex="-1" aria-labelledby="createTaxGroupLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.taxgroups.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createTaxGroupLabel">New Tax Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Percentage</label>
                        <input type="number" step="0.01" name="percentage" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
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
<div class="modal fade" id="editTaxGroupModal" tabindex="-1" aria-labelledby="editTaxGroupLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editTaxGroupForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editTaxGroupLabel">Edit Tax Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Percentage</label>
                        <input type="number" step="0.01" name="percentage" id="edit_percentage" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
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
            $('#edit_name').val($(this).data('name'));
            $('#edit_percentage').val($(this).data('percentage'));
            $('#edit_description').val($(this).data('description'));

            const action = "{{ route('admin.taxgroups.update', ':id') }}".replace(':id', id);
            $('#editTaxGroupForm').attr('action', action);

            $('#editTaxGroupModal').modal('show');
        });
    });
</script>
@endpush
