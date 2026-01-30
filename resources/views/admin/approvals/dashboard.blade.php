@extends('layout.master')

@section('title', 'Approval Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Pending Approvals</h4>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-warning bg-opacity-10">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $statistics['total_pending'] }}</h3>
                                    <small class="text-muted">Total Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary bg-opacity-10">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $statistics['budget_change_orders'] }}</h3>
                                    <small class="text-muted">Budget Change Orders</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info bg-opacity-10">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $statistics['po_change_orders'] }}</h3>
                                    <small class="text-muted">PO Change Orders</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger bg-opacity-10">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $statistics['overdue'] }}</h3>
                                    <small class="text-muted">Overdue (>48hrs)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="BudgetChangeOrder" {{ request('type') == 'BudgetChangeOrder' ? 'selected' : '' }}>
                                        Budget Change Orders
                                    </option>
                                    <option value="PoChangeOrder" {{ request('type') == 'PoChangeOrder' ? 'selected' : '' }}>
                                        PO Change Orders
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="project_id" class="form-select">
                                    <option value="">All Projects</option>
                                    @foreach($projects as $proj)
                                        <option value="{{ $proj->proj_id }}" {{ request('project_id') == $proj->proj_id ? 'selected' : '' }}>
                                            {{ $proj->proj_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="priority" class="form-select">
                                    <option value="">All Priorities</option>
                                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High Priority</option>
                                    <option value="overdue" {{ request('priority') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Approvals List -->
                    @if($approvals->isEmpty())
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> No pending approvals at this time.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Request #</th>
                                        <th>Project</th>
                                        <th>Description</th>
                                        <th>Amount</th>
                                        <th>Level</th>
                                        <th>Requested</th>
                                        <th>Age</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvals as $approval)
                                        @php
                                            $approvable = $approval->approvable;
                                            $age = now()->diffInHours($approval->apreq_created_at);
                                            $isOverdue = $age > 48;
                                        @endphp
                                        <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                            <td>
                                                <span class="badge bg-{{ $approval->apreq_type == 'BudgetChangeOrder' ? 'primary' : 'info' }}">
                                                    {{ $approval->apreq_type == 'BudgetChangeOrder' ? 'BCO' : 'PCO' }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>
                                                    @if($approval->apreq_type == 'BudgetChangeOrder')
                                                        {{ $approvable->bco_number ?? 'N/A' }}
                                                    @else
                                                        {{ $approvable->poco_number ?? 'N/A' }}
                                                    @endif
                                                </strong>
                                            </td>
                                            <td>
                                                @if($approval->apreq_type == 'BudgetChangeOrder')
                                                    {{ $approvable->project->proj_name ?? 'N/A' }}
                                                @else
                                                    {{ $approvable->purchaseOrder->project->proj_name ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($approval->apreq_type == 'BudgetChangeOrder')
                                                    <small>{{ $approvable->costCode->full_code ?? 'N/A' }}</small><br>
                                                    <small class="text-muted">{{ Str::limit($approvable->bco_reason ?? '', 50) }}</small>
                                                @else
                                                    <small>PO: {{ $approvable->purchaseOrder->porder_no ?? 'N/A' }}</small><br>
                                                    <small class="text-muted">{{ Str::limit($approvable->poco_reason ?? '', 50) }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($approval->apreq_type == 'BudgetChangeOrder')
                                                    <span class="{{ $approvable->bco_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $approvable->bco_amount >= 0 ? '+' : '' }}${{ number_format(abs($approvable->bco_amount ?? 0), 2) }}
                                                    </span>
                                                @else
                                                    <span class="{{ $approvable->poco_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ $approvable->poco_amount >= 0 ? '+' : '' }}${{ number_format(abs($approvable->poco_amount ?? 0), 2) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    Level {{ $approval->apreq_level }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($approval->apreq_created_at)->format('m/d/Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $isOverdue ? 'danger' : 'warning' }}">
                                                    {{ $age }}h
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.approvals.show', $approval->apreq_id) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $approvals->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recently Processed Approvals -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recently Processed (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    @if($recentlyProcessed->isEmpty())
                        <p class="text-muted">No approvals processed in the last 7 days.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Request #</th>
                                        <th>Action</th>
                                        <th>By</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentlyProcessed as $processed)
                                        <tr>
                                            <td>
                                                <span class="badge bg-{{ $processed->apreq_type == 'BudgetChangeOrder' ? 'primary' : 'info' }}">
                                                    {{ $processed->apreq_type == 'BudgetChangeOrder' ? 'BCO' : 'PCO' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($processed->approvable)
                                                    <strong>
                                                        @if($processed->apreq_type == 'BudgetChangeOrder')
                                                            {{ $processed->approvable->bco_number ?? 'N/A' }}
                                                        @else
                                                            {{ $processed->approvable->poco_number ?? 'N/A' }}
                                                        @endif
                                                    </strong>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $processed->apreq_status == 'approved' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($processed->apreq_status) }}
                                                </span>
                                            </td>
                                            <td>{{ $processed->approver->u_name ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($processed->apreq_approved_at)->format('m/d/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
