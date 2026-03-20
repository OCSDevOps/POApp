@extends('layouts.admin')

@section('title', 'Approval Workflows')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1"><i class="fas fa-sitemap"></i> Approval Workflows</h1>
                    <p class="text-muted mb-0">Configure approval workflows for budget and PO change orders</p>
                </div>
                <a href="{{ route('admin.approval-workflows.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Workflow
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

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.approval-workflows.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="type" class="form-label">Workflow Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="budget_change_order" {{ request('type') == 'budget_change_order' ? 'selected' : '' }}>Budget Change Orders</option>
                        <option value="po_change_order" {{ request('type') == 'po_change_order' ? 'selected' : '' }}>PO Change Orders</option>
                        <option value="purchase_order" {{ request('type') == 'purchase_order' ? 'selected' : '' }}>Purchase Orders</option>
                        <option value="contract_co" {{ request('type') == 'contract_co' ? 'selected' : '' }}>Contract Change Orders</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="project_id" class="form-label">Project</label>
                    <select name="project_id" id="project_id" class="form-select">
                        <option value="">All Projects (Company-Wide)</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->proj_id }}" {{ request('project_id') == $project->proj_id ? 'selected' : '' }}>
                                {{ $project->proj_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.approval-workflows.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Workflows List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Configured Workflows ({{ $workflows->count() }})</h5>
        </div>
        <div class="card-body">
            @if($workflows->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No approval workflows configured. Create one to get started.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>Scope</th>
                                <th>Amount Range</th>
                                <th>Approval Method</th>
                                <th>Approvers</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workflows as $workflow)
                                <tr>
                                    <td>
                                        @if($workflow->workflow_type == 'budget_change_order')
                                            <span class="badge bg-info">BCO</span>
                                        @elseif($workflow->workflow_type == 'po_change_order')
                                            <span class="badge bg-warning">PCO</span>
                                        @elseif($workflow->workflow_type == 'contract_co')
                                            <span class="badge bg-success">CCO</span>
                                        @else
                                            <span class="badge bg-primary">PO</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($workflow->project_id)
                                            <strong>{{ $workflow->project->proj_name ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">Project-Specific</small>
                                        @else
                                            <span class="text-muted">Company-Wide</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>${{ number_format($workflow->amount_threshold_min, 2) }}</strong>
                                        @if($workflow->amount_threshold_max)
                                            to <strong>${{ number_format($workflow->amount_threshold_max, 2) }}</strong>
                                        @else
                                            <span class="text-muted">and above</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($workflow->approver_roles)
                                            <span class="badge bg-success">Role-Based</span>
                                        @else
                                            <span class="badge bg-secondary">User-Based</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($workflow->approver_roles)
                                            @php
                                                $roles = is_array($workflow->approver_roles) ? $workflow->approver_roles : json_decode($workflow->approver_roles, true);
                                            @endphp
                                            @if(is_array($roles))
                                                @foreach($roles as $role)
                                                    <span class="badge bg-light text-dark">{{ ucwords(str_replace('_', ' ', $role)) }}</span>
                                                @endforeach
                                            @endif
                                        @else
                                            @php
                                                $approverIds = is_array($workflow->approver_user_ids) ? $workflow->approver_user_ids : json_decode($workflow->approver_user_ids, true);
                                                $approvers = $approverIds ? \App\Models\User::whereIn('id', $approverIds)->get() : collect();
                                            @endphp
                                            <small>
                                                @foreach($approvers as $index => $approver)
                                                    {{ $approver->name }}@if(!$loop->last), @endif
                                                @endforeach
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($workflow->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.approval-workflows.edit', $workflow->workflow_id) }}"
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.approval-workflows.toggle-status', $workflow->workflow_id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-{{ $workflow->is_active ? 'warning' : 'success' }}"
                                                        title="{{ $workflow->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="fas fa-{{ $workflow->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-outline-danger delete-btn" title="Delete"
                                                    data-url="{{ route('admin.approval-workflows.destroy', $workflow->workflow_id) }}" data-name="workflow #{{ $workflow->workflow_id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Info Box -->
    <div class="card mt-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-info-circle"></i> How Approval Workflows Work</h6>
        </div>
        <div class="card-body">
            <ul class="mb-0">
                <li><strong>Company-Wide:</strong> Applies to all projects that don't have project-specific workflows</li>
                <li><strong>Project-Specific:</strong> Overrides company-wide workflows for a specific project</li>
                <li><strong>Role-Based:</strong> Automatically assigns approvers based on their project role</li>
                <li><strong>User-Based:</strong> Fixed list of specific users who must approve</li>
                <li><strong>Amount Ranges:</strong> Different workflows trigger based on the change order amount</li>
                <li><strong>Multi-Level:</strong> Up to 3 approval levels can be configured per workflow</li>
            </ul>
        </div>
    </div>
</div>
@include('partials.delete-modal')
@endsection
