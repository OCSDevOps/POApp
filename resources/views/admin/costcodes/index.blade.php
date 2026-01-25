@extends('layouts.admin')

@section('title', 'Cost Codes')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Cost Codes</h4>
                <p class="text-muted mb-0">Manage project cost codes used across purchase orders, budgets, and RFQs.</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCostCodeModal">
                <i class="fa fa-plus me-1"></i> New Cost Code
            </button>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">All Cost Codes</div>
            <div class="card-body table-responsive">
                <table class="table table-striped datatable align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($costCodes as $code)
                            <tr>
                                <td>{{ $code->cc_id }}</td>
                                <td>{{ $code->cc_no }}</td>
                                <td>{{ $code->cc_description }}</td>
                                <td>
                                    @if($code->cc_status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Locked</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary me-1 edit-btn"
                                            data-id="{{ $code->cc_id }}"
                                            data-no="{{ $code->cc_no }}"
                                            data-description="{{ $code->cc_description }}"
                                            data-status="{{ $code->cc_status }}">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <form action="{{ route('admin.costcodes.destroy', $code) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this cost code?')">
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
<div class="modal fade" id="createCostCodeModal" tabindex="-1" aria-labelledby="createCostCodeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.costcodes.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createCostCodeLabel">New Cost Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" name="cc_no" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="cc_description" class="form-control" rows="3" required></textarea>
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
<div class="modal fade" id="editCostCodeModal" tabindex="-1" aria-labelledby="editCostCodeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editCostCodeForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="cc_status" value="0">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCostCodeLabel">Edit Cost Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" name="cc_no" id="edit_cc_no" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="cc_description" id="edit_cc_description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="edit_cc_status" name="cc_status_toggle" value="1">
                        <label class="form-check-label" for="edit_cc_status">Active</label>
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
            $('#edit_cc_no').val($(this).data('no'));
            $('#edit_cc_description').val($(this).data('description'));
            const isActive = $(this).data('status') == 1;
            $('#edit_cc_status').prop('checked', isActive);
            $('#editCostCodeForm').find('input[name="cc_status"][type="hidden"]').val(isActive ? 1 : 0);

            const action = "{{ route('admin.costcodes.update', ':id') }}".replace(':id', id);
            $('#editCostCodeForm').attr('action', action);

            $('#editCostCodeModal').modal('show');
        });

        // Keep hidden value in sync with switch so unchecked sends 0
        $('#edit_cc_status').on('change', function () {
            $('#editCostCodeForm').find('input[name="cc_status"][type="hidden"]').val(this.checked ? 1 : 0);
        });
    });
</script>
@endpush
