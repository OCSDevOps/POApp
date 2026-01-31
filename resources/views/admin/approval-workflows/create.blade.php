@extends('layouts.admin')

@section('title', 'Create Approval Workflow')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0"><i class="fas fa-sitemap"></i> Create Approval Workflow</h1>
                <a href="{{ route('admin.approval-workflows.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Workflows
                </a>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.approval-workflows.store') }}" method="POST" id="workflowForm">
        @csrf
        
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
                                    <option value="budget_change_order">Budget Change Order (BCO)</option>
                                    <option value="po_change_order">PO Change Order (PCO)</option>
                                    <option value="purchase_order">Purchase Order (PO)</option>
                                </select>
                                <small class="text-muted">Choose what type of transaction this workflow applies to</small>
                            </div>

                            <div class="col-md-6">
                                <label for="project_id" class="form-label">Project Scope</label>
                                <select name="project_id" id="project_id" class="form-select">
                                    <option value="">Company-Wide (All Projects)</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->proj_id }}">{{ $project->proj_name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Leave blank for company-wide, or select a specific project</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="threshold_from" class="form-label">Minimum Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="threshold_from" id="threshold_from" 
                                           class="form-control" step="0.01" min="0" required value="0">
                                </div>
                                <small class="text-muted">Workflow triggers when amount is equal to or greater than this</small>
                            </div>

                            <div class="col-md-6">
                                <label for="threshold_to" class="form-label">Maximum Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="threshold_to" id="threshold_to" 
                                           class="form-control" step="0.01" min="0" placeholder="Leave blank for no limit">
                                </div>
                                <small class="text-muted">Leave blank to apply to all amounts above minimum</small>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label">Approval Method <span class="text-danger">*</span></label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="approval_type" id="approval_type_role" value="role_based" autocomplete="off" checked>
                                <label class="btn btn-outline-primary" for="approval_type_role">
                                    <i class="fas fa-users"></i> Role-Based
                                </label>

                                <input type="radio" class="btn-check" name="approval_type" id="approval_type_user" value="user_based" autocomplete="off">
                                <label class="btn btn-outline-primary" for="approval_type_user">
                                    <i class="fas fa-user"></i> User-Based
                                </label>
                            </div>
                            <small class="text-muted d-block mt-1">
                                Role-based: Dynamically assigns approvers based on project roles | User-based: Fixed list of specific users
                            </small>
                        </div>

                        <!-- Role-Based Approvers -->
                        <div id="role_based_section">
                            <label class="form-label">Approver Roles <span class="text-danger">*</span></label>
                            <div class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="approver_roles[]" value="project_manager" id="role_pm">
                                        <label class="form-check-label" for="role_pm">Project Manager</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="approver_roles[]" value="manager" id="role_manager">
                                        <label class="form-check-label" for="role_manager">Manager</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="approver_roles[]" value="director" id="role_director">
                                        <label class="form-check-label" for="role_director">Director</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="approver_roles[]" value="finance" id="role_finance">
                                        <label class="form-check-label" for="role_finance">Finance</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="approver_roles[]" value="executive" id="role_executive">
                                        <label class="form-check-label" for="role_executive">Executive</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="approver_roles[]" value="admin" id="role_admin">
                                        <label class="form-check-label" for="role_admin">Admin</label>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">Select the roles that can approve this workflow</small>
                        </div>

                        <!-- User-Based Approvers -->
                        <div id="user_based_section" style="display: none;">
                            <div class="mb-3">
                                <label for="approver_user_1" class="form-label">Level 1 Approver <span class="text-danger">*</span></label>
                                <select name="approver_user_1" id="approver_user_1" class="form-select">
                                    <option value="">Select user...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->u_id }}">{{ $user->u_name }} ({{ $user->u_email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="approver_user_2" class="form-label">Level 2 Approver (Optional)</label>
                                <select name="approver_user_2" id="approver_user_2" class="form-select">
                                    <option value="">None (Single-level approval)</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->u_id }}">{{ $user->u_name }} ({{ $user->u_email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="approver_user_3" class="form-label">Level 3 Approver (Optional)</label>
                                <select name="approver_user_3" id="approver_user_3" class="form-select">
                                    <option value="">None (Two-level approval)</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->u_id }}">{{ $user->u_name }} ({{ $user->u_email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="require_all" id="require_all" value="1">
                                <label class="form-check-label" for="require_all">
                                    Require all approvers to approve (for multi-level workflows)
                                </label>
                            </div>
                            <small class="text-muted">If checked, all levels must approve. If unchecked, any approver can approve.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Workflow
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
                        <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Workflow Tips</h6>
                    </div>
                    <div class="card-body">
                        <h6>Example Configurations:</h6>
                        <div class="mb-3">
                            <strong>Small Changes ($0 - $5,000)</strong>
                            <ul class="small mb-0">
                                <li>Min: $0, Max: $5,000</li>
                                <li>Role: Project Manager</li>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <strong>Medium Changes ($5,000 - $25,000)</strong>
                            <ul class="small mb-0">
                                <li>Min: $5,000, Max: $25,000</li>
                                <li>Roles: Manager + Finance</li>
                            </ul>
                        </div>
                        <div class="mb-3">
                            <strong>Large Changes ($25,000+)</strong>
                            <ul class="small mb-0">
                                <li>Min: $25,000, Max: (blank)</li>
                                <li>Roles: Director + Finance + Executive</li>
                            </ul>
                        </div>

                        <hr>

                        <h6>Best Practices:</h6>
                        <ul class="small mb-0">
                            <li>Create non-overlapping amount ranges</li>
                            <li>Use role-based for flexibility</li>
                            <li>Project-specific workflows override company-wide</li>
                            <li>Test workflows before making them active</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Toggle between role-based and user-based
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

    // Form validation
    $('#workflowForm').submit(function(e) {
        const approvalType = $('input[name="approval_type"]:checked').val();
        
        if(approvalType === 'role_based') {
            const rolesChecked = $('input[name="approver_roles[]"]:checked').length;
            if(rolesChecked === 0) {
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

    // Validate threshold range
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
@endsection
