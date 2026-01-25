@extends('layouts.admin')

@section('title', 'Permission Templates')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Permission Templates</h4>
                <p class="text-muted mb-0">Manage permission templates used to gate access across modules.</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPermissionModal">
                <i class="fa fa-plus me-1"></i> New Template
            </button>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">All Templates</div>
            <div class="card-body table-responsive">
                <table class="table table-striped datatable align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>PO</th>
                            <th>RO</th>
                            <th>RFQ</th>
                            <th>Items</th>
                            <th>Projects</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($templates as $tpl)
                            <tr>
                                <td>{{ $tpl->pt_id }}</td>
                                <td>{{ $tpl->pt_template_name }}</td>
                                <td>{{ $tpl->pt_t_porder }}</td>
                                <td>{{ $tpl->pt_t_rorder }}</td>
                                <td>{{ $tpl->pt_t_rfq }}</td>
                                <td>{{ $tpl->pt_m_item }}</td>
                                <td>{{ $tpl->pt_m_projects }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary me-1 edit-btn"
                                            data-id="{{ $tpl->pt_id }}"
                                            data-name="{{ $tpl->pt_template_name }}"
                                            data-porder="{{ $tpl->pt_t_porder }}"
                                            data-rorder="{{ $tpl->pt_t_rorder }}"
                                            data-rcorder="{{ $tpl->pt_t_rcorder }}"
                                            data-rfq="{{ $tpl->pt_t_rfq }}"
                                            data-mitem="{{ $tpl->pt_m_item }}"
                                            data-muom="{{ $tpl->pt_m_uom }}"
                                            data-mcost="{{ $tpl->pt_m_costcode }}"
                                            data-mproj="{{ $tpl->pt_m_projects }}"
                                            data-msupp="{{ $tpl->pt_m_suppliers }}"
                                            data-mtax="{{ $tpl->pt_m_taxgroup }}"
                                            data-mbudget="{{ $tpl->pt_m_budget }}"
                                            data-memail="{{ $tpl->pt_m_email }}"
                                            data-iitem="{{ $tpl->pt_i_item }}"
                                            data-iitemp="{{ $tpl->pt_i_itemp }}"
                                            data-isupc="{{ $tpl->pt_i_supplierc }}"
                                            data-eeq="{{ $tpl->pt_e_eq }}"
                                            data-eeqm="{{ $tpl->pt_e_eqm }}"
                                            data-echeck="{{ $tpl->pt_e_checklist }}"
                                            data-auser="{{ $tpl->pt_a_user }}"
                                            data-aperm="{{ $tpl->pt_a_permissions }}"
                                            data-acinfo="{{ $tpl->pt_a_cinfo }}"
                                            data-aprocore="{{ $tpl->pt_a_procore }}">
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <form action="{{ route('admin.permissions.destroy', $tpl) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Archive this template?')">
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
<div class="modal fade" id="createPermissionModal" tabindex="-1" aria-labelledby="createPermissionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.permissions.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createPermissionLabel">New Permission Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.permissions.partials.form-fields')
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
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editPermissionForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editPermissionLabel">Edit Permission Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.permissions.partials.form-fields')
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
            $('#pt_template_name').val($(this).data('name'));
            $('#pt_t_porder').val($(this).data('porder'));
            $('#pt_t_rorder').val($(this).data('rorder'));
            $('#pt_t_rcorder').val($(this).data('rcorder'));
            $('#pt_t_rfq').val($(this).data('rfq'));
            $('#pt_m_item').val($(this).data('mitem'));
            $('#pt_m_uom').val($(this).data('muom'));
            $('#pt_m_costcode').val($(this).data('mcost'));
            $('#pt_m_projects').val($(this).data('mproj'));
            $('#pt_m_suppliers').val($(this).data('msupp'));
            $('#pt_m_taxgroup').val($(this).data('mtax'));
            $('#pt_m_budget').val($(this).data('mbudget'));
            $('#pt_m_email').val($(this).data('memail'));
            $('#pt_i_item').val($(this).data('iitem'));
            $('#pt_i_itemp').val($(this).data('iitemp'));
            $('#pt_i_supplierc').val($(this).data('isupc'));
            $('#pt_e_eq').val($(this).data('eeq'));
            $('#pt_e_eqm').val($(this).data('eeqm'));
            $('#pt_e_checklist').val($(this).data('echeck'));
            $('#pt_a_user').val($(this).data('auser'));
            $('#pt_a_permissions').val($(this).data('aperm'));
            $('#pt_a_cinfo').val($(this).data('acinfo'));
            $('#pt_a_procore').val($(this).data('aprocore'));

            const action = "{{ route('admin.permissions.update', ':id') }}".replace(':id', id);
            $('#editPermissionForm').attr('action', action);

            $('#editPermissionModal').modal('show');
        });
    });
</script>
@endpush
