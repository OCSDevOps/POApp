@extends('layouts.admin')

@section('title', 'Edit Approval Workflow')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0"><i class="fas fa-sitemap"></i> Edit Approval Workflow</h1>
                <a href="{{ route('admin.approval-workflows.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Workflows
                </a>
            </div>
        </div>
    </div>

    @include('partials.validation-errors')
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.approval-workflows.update', $workflow->workflow_id) }}" method="POST" id="workflowForm">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Workflow Configuration</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="type" class="form-label">Workflow Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-select" required>
                                    <option value="">Select type...</option>
                                    <option value="budget_change_order" @selected(old('type', $workflow->workflow_type) == 'budget_change_order')>Budget Change Order (BCO)</option>
                                    <option value="po_change_order" @selected(old('type', $workflow->workflow_type) == 'po_change_order')>PO Change Order (PCO)</option>
                                    <option value="purchase_order" @selected(old('type', $workflow->workflow_type) == 'purchase_order')>Purchase Order (PO)</option>
                                    <option value="contract_co" @selected(old('type', $workflow->workflow_type) == 'contract_co')>Contract Change Order (CCO)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="project_id" class="form-label">Project Scope</label>
                                <select name="project_id" id="project_id" class="form-select">
                                    <option value="">Company-Wide (All Projects)</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->proj_id }}" @selected(old('project_id', $workflow->project_id) == $project->proj_id)>{{ $project->proj_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="threshold_from" class="form-label">Minimum Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="threshold_from" id="threshold_from"
                                           class="form-control" step="0.01" min="0" required
                                           value="{{ old('threshold_from', $workflow->amount_threshold_min) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="threshold_to" class="form-label">Maximum Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="threshold_to" id="threshold_to"
                                           class="form-control" step="0.01" min="0"
                                           value="{{ old('threshold_to', $workflow->amount_threshold_max) }}"
                                           placeholder="Leave blank for no limit">
                                </div>
                            </div>
                        </div>

                        <hr>

                        @php
                            $currentApprovalType = $workflow->approver_roles ? 'role_based' : 'user_based';
                            $currentRoles = $workflow->approver_roles ? (is_array($workflow->approver_roles) ? $workflow->approver_roles : json_decode($workflow->approver_roles, true)) : [];
                        @endphp

                        <div class="mb-3">
                            <label class="form-label">Approval Method <span class="text-danger">*</span></label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="approval_type" id="approval_type_role" value="role_based" autocomplete="off" @checked($currentApprovalType == 'role_based')>
                                <label class="btn btn-outline-primary" for="approval_type_role">
                                    <i class="fas fa-users"></i> Role-Based
                                </label>

                                <input type="radio" class="btn-check" name="approval_type" id="approval_type_user" value="user_based" autocomplete="off" @checked($currentApprovalType == 'user_based')>
                                <label class="btn btn-outline-primary" for="approval_type_user">
                                    <i class="fas fa-user"></i> User-Based
                                </label>
                            </div>
                        </div>

                        <!-- Role-Based Approvers -->
                        <div id="role_based_section" @if($currentApprovalType != 'role_based') style="display: none;" @endif>
                            <label class="form-label">Approver Roles <span class="text-danger">*</span></label>
                            <div class="row g-2 mb-3">
                                @foreach(['project_manager' => 'Project Manager', 'manager' => 'Manager', 'director' => 'Director', 'finance' => 'Finance', 'executive' => 'Executive', 'admin' => 'Admin'] as $role => $label)
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="approver_roles[]" value="{{ $role }}" id="role_{{ $role }}" @checked(in_array($role, $currentRoles))>
                                            <label class="form-check-label" for="role_{{ $role }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- User-Based Approvers -->
                        @php
                            $approverUserIds = is_array($workflow->approver_user_ids) ? $workflow->approver_user_ids : json_decode($workflow->approver_user_ids, true);
                            $approverUserIds = $approverUserIds ?? [];
                        @endphp
                        <div id="user_based_section" @if($currentApprovalType != 'user_based') style="display: none;" @endif>
                            <div class="mb-3">
                                <label for="approver_user_1" class="form-label">Level 1 Approver <span class="text-danger">*</span></label>
                                <select name="approver_user_1" id="approver_user_1" class="form-select">
                                    <option value="">Select user...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('approver_user_1', $approverUserIds[0] ?? null) == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="approver_user_2" class="form-label">Level 2 Approver (Optional)</label>
                                <select name="approver_user_2" id="approver_user_2" class="form-select">
                                    <option value="">None (Single-level approval)</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('approver_user_2', $approverUserIds[1] ?? null) == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="approver_user_3" class="form-label">Level 3 Approver (Optional)</label>
                                <select name="approver_user_3" id="approver_user_3" class="form-select">
                                    <option value="">None (Two-level approval)</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('approver_user_3', $approverUserIds[2] ?? null) == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="require_all" id="require_all" value="1" @checked($workflow->approval_logic == 'all')>
                                <label class="form-check-label" for="require_all">
                                    Require all approvers to approve (for multi-level workflows)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Workflow
                        </button>
                        <a href="{{ route('admin.approval-workflows.index') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Workflow Info</h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-1"><strong>Created:</strong> {{ $workflow->created_at ?? 'N/A' }}</p>
                        <p class="small mb-1"><strong>Last Updated:</strong> {{ $workflow->updated_at ?? 'N/A' }}</p>
                        <p class="small mb-0"><strong>Status:</strong>
                            @if($workflow->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('input[name="approval_type"]').change(function() {
        if($(this).val() === 'role_based') {
            $('#role_based_section').slideDown();
            $('#user_based_section').slideUp();
            $('#approver_user_1').removeAttr('required');
        } else {
            $('#role_based_section').slideUp();
            $('#user_based_section').slideDown();
            $('#approver_user_1').attr('required', 'required');
        }
    });

    $('#workflowForm').submit(function(e) {
        const approvalType = $('input[name="approval_type"]:checked').val();
        if(approvalType === 'role_based') {
            if($('input[name="approver_roles[]"]:checked').length === 0) {
                e.preventDefault();
                alert('Please select at least one approver role.');
                return false;
            }
        } else {
            if(!$('#approver_user_1').val()) {
                e.preventDefault();
                alert('Please select at least a Level 1 approver.');
                return false;
            }
        }
    });

    $('#threshold_to').on('blur', function() {
        const from = parseFloat($('#threshold_from').val()) || 0;
        const to = parseFloat($(this).val()) || 0;
        if(to > 0 && to <= from) {
            alert('Maximum amount must be greater than minimum amount.');
            $(this).val('');
        }
    });
});
</script>
@endpush
