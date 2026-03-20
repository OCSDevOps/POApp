@extends('layouts.admin')

@section('title', 'Manage Project Roles - ' . $project->proj_name)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1"><i class="fas fa-users-cog"></i> Project Role Assignments</h1>
                    <p class="text-muted mb-0">
                        <strong>Project:</strong> {{ $project->proj_name }} ({{ $project->proj_number }})
                    </p>
                </div>
                <a href="{{ route('admin.project-roles.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Change Project
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Add Role Form -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Assign Role</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.project-roles.store') }}" method="POST" id="roleForm">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->proj_id }}">

                        <div class="mb-3">
                            <label for="user_id" class="form-label">User <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">Select user...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-select" required>
                                <option value="">Select role...</option>
                                <option value="staff">Staff</option>
                                <option value="project_manager">Project Manager</option>
                                <option value="manager">Manager</option>
                                <option value="director">Director</option>
                                <option value="finance">Finance</option>
                                <option value="executive">Executive</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="can_approve" id="can_approve" value="1">
                                <label class="form-check-label" for="can_approve">
                                    Can Approve
                                </label>
                            </div>
                            <small class="text-muted">User can approve budget change orders and PO change orders</small>
                        </div>

                        <div class="mb-3" id="approval_limit_wrapper" style="display: none;">
                            <label for="approval_limit" class="form-label">Approval Limit</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="approval_limit" id="approval_limit" 
                                       class="form-control" step="0.01" min="0" 
                                       placeholder="Leave blank for unlimited">
                            </div>
                            <small class="text-muted">Maximum amount this user can approve (leave blank for no limit)</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Assign Role
                        </button>
                    </form>
                </div>
            </div>

            <!-- Role Descriptions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Role Descriptions</h6>
                </div>
                <div class="card-body p-2">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td><strong>Staff:</strong></td>
                                    <td>General access, limited approval rights</td>
                                </tr>
                                <tr>
                                    <td><strong>Project Manager:</strong></td>
                                    <td>Manages day-to-day operations</td>
                                </tr>
                                <tr>
                                    <td><strong>Manager:</strong></td>
                                    <td>Approves medium-value changes</td>
                                </tr>
                                <tr>
                                    <td><strong>Director:</strong></td>
                                    <td>Approves high-value changes</td>
                                </tr>
                                <tr>
                                    <td><strong>Finance:</strong></td>
                                    <td>Budget oversight and approval</td>
                                </tr>
                                <tr>
                                    <td><strong>Executive:</strong></td>
                                    <td>Final approval authority</td>
                                </tr>
                                <tr>
                                    <td><strong>Admin:</strong></td>
                                    <td>Full administrative access</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Assignments -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Current Role Assignments</h5>
                </div>
                <div class="card-body">
                    @if($roles->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No role assignments found. Use the form to assign roles to users.
                        </div>
                    @else
                        @foreach(['admin', 'executive', 'finance', 'director', 'manager', 'project_manager', 'staff'] as $roleType)
                            @if(isset($roles[$roleType]) && $roles[$roleType]->isNotEmpty())
                                <div class="mb-4">
                                    <h6 class="text-uppercase text-muted mb-3">
                                        <i class="fas fa-user-tag"></i> {{ ucwords(str_replace('_', ' ', $roleType)) }}
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>User</th>
                                                    <th>Can Approve</th>
                                                    <th>Approval Limit</th>
                                                    <th>Assigned</th>
                                                    <th width="100">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($roles[$roleType] as $assignment)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $assignment->user->name }}</strong><br>
                                                            <small class="text-muted">{{ $assignment->user->email }}</small>
                                                        </td>
                                                        <td>
                                                            @if($assignment->pr_can_approve)
                                                                <span class="badge bg-success">Yes</span>
                                                            @else
                                                                <span class="badge bg-secondary">No</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($assignment->pr_approval_limit)
                                                                ${{ number_format($assignment->pr_approval_limit, 2) }}
                                                            @else
                                                                <span class="text-muted">Unlimited</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                {{ $assignment->pr_created_at ? $assignment->pr_created_at->format('M d, Y') : 'N/A' }}
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                                                        onclick="editRole({{ $assignment->pr_id }}, '{{ $assignment->pr_can_approve }}', '{{ $assignment->pr_approval_limit }}')"
                                                                        title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <form action="{{ route('admin.project-roles.destroy', $assignment->pr_id) }}" 
                                                                      method="POST" class="d-inline" 
                                                                      onsubmit="return confirm('Remove this role assignment?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Remove">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Role Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="can_approve" id="edit_can_approve" value="1">
                            <label class="form-check-label" for="edit_can_approve">
                                Can Approve
                            </label>
                        </div>
                    </div>

                    <div class="mb-3" id="edit_approval_limit_wrapper">
                        <label for="edit_approval_limit" class="form-label">Approval Limit</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="approval_limit" id="edit_approval_limit" 
                                   class="form-control" step="0.01" min="0" 
                                   placeholder="Leave blank for unlimited">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle approval limit field
    $('#can_approve').change(function() {
        if($(this).is(':checked')) {
            $('#approval_limit_wrapper').slideDown();
        } else {
            $('#approval_limit_wrapper').slideUp();
            $('#approval_limit').val('');
        }
    });

    $('#edit_can_approve').change(function() {
        if(!$(this).is(':checked')) {
            $('#edit_approval_limit').val('');
        }
    });
});

function editRole(id, canApprove, approvalLimit) {
    const modal = new bootstrap.Modal(document.getElementById('editRoleModal'));
    const form = document.getElementById('editRoleForm');
    
    form.action = '{{ route("admin.project-roles.index") }}/' + id;
    
    document.getElementById('edit_can_approve').checked = canApprove == '1';
    document.getElementById('edit_approval_limit').value = approvalLimit || '';
    
    modal.show();
}
</script>
@endpush
