@extends('layouts.admin')

@section('title', 'Budget Change Order - ' . $changeOrder->bco_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Budget Change Order: {{ $changeOrder->bco_number }}</h4>
                    <div>
                        @if($changeOrder->isEditable())
                            <form method="POST" action="{{ route('admin.budget-change-orders.submit', ['projectId' => $changeOrder->bco_project_ms, 'id' => $changeOrder->bco_id]) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm">
                                    <i class="fas fa-paper-plane"></i> Submit for Approval
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.budget-change-orders.cancel', ['projectId' => $changeOrder->bco_project_ms, 'id' => $changeOrder->bco_id]) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this BCO?')">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.budget-change-orders.index', $changeOrder->bco_project_ms) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- BCO Status Banner -->
                    <div class="alert alert-{{ $changeOrder->bco_status == 'approved' ? 'success' : ($changeOrder->bco_status == 'rejected' ? 'danger' : 'warning') }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Status:</strong> {{ str_replace('_', ' ', ucfirst($changeOrder->bco_status)) }}
                            </div>
                            @if($changeOrder->submitted_at)
                                <div><strong>Submitted:</strong> {{ $changeOrder->submitted_at->format('m/d/Y h:i A') }}</div>
                            @endif
                            @if($changeOrder->approved_at)
                                <div><strong>Approved:</strong> {{ $changeOrder->approved_at->format('m/d/Y h:i A') }} by {{ $changeOrder->approver->user_name ?? 'Unknown' }}</div>
                            @endif
                        </div>
                    </div>

                    <!-- BCO Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Change Order Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th style="width: 40%;">BCO Number:</th>
                                            <td><strong>{{ $changeOrder->bco_number }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Project:</th>
                                            <td>{{ $changeOrder->project->proj_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Cost Code:</th>
                                            <td>{{ $changeOrder->budget->costCode->getFormattedCode() }} - {{ $changeOrder->budget->costCode->cc_description }}</td>
                                        </tr>
                                        <tr>
                                            <th>Type:</th>
                                            <td>
                                                <span class="badge 
                                                    @if($changeOrder->bco_type == 'increase') bg-success
                                                    @elseif($changeOrder->bco_type == 'decrease') bg-warning
                                                    @else bg-info
                                                    @endif">
                                                    {{ ucfirst($changeOrder->bco_type) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created By:</th>
                                            <td>{{ $changeOrder->creator->user_name ?? 'Unknown' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created Date:</th>
                                            <td>{{ $changeOrder->created_at->format('m/d/Y h:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Budget Impact</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <th style="width: 40%;">Previous Budget:</th>
                                            <td class="text-end">${{ number_format($changeOrder->bco_previous_budget, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>New Budget:</th>
                                            <td class="text-end">${{ number_format($changeOrder->bco_new_budget, 2) }}</td>
                                        </tr>
                                        <tr class="table-light">
                                            <th>Change Amount:</th>
                                            <td class="text-end">
                                                <strong class="fs-5 {{ $changeOrder->bco_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $changeOrder->bco_amount >= 0 ? '+' : '' }}${{ number_format(abs($changeOrder->bco_amount), 2) }}
                                                </strong>
                                            </td>
                                        </tr>
                                    </table>

                                    @if($changeOrder->bco_transfer_from_cc_ms)
                                        <div class="alert alert-info mt-3">
                                            <strong>Transfer From:</strong> Cost Code {{ $changeOrder->transferFromCostCode->getFormattedCode() }}<br>
                                            <strong>Transfer Amount:</strong> ${{ number_format($changeOrder->bco_transfer_amount, 2) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Reason for Change</h6>
                        </div>
                        <div class="card-body">
                            <p>{{ $changeOrder->bco_reason }}</p>
                        </div>
                    </div>

                    <!-- Approval Section -->
                    @if($changeOrder->approvalRequest)
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Approval Workflow</h6>
                                @if($changeOrder->approvalRequest->isPending())
                                    <span class="badge bg-warning">Level {{ $changeOrder->approvalRequest->current_level }} of {{ $changeOrder->approvalRequest->required_levels }}</span>
                                @endif
                            </div>
                            <div class="card-body">
                                @if($approvalHistory && count($approvalHistory) > 0)
                                    <div class="timeline">
                                        @foreach($approvalHistory as $action)
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-{{ $action['action'] == 'approve' ? 'success' : 'danger' }}"></div>
                                                <div class="timeline-content">
                                                    <strong>{{ $action['user_name'] }}</strong> 
                                                    <span class="badge bg-{{ $action['action'] == 'approve' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($action['action']) }}d
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($action['timestamp'])->format('m/d/Y h:i A') }}</small>
                                                    @if($action['comments'])
                                                        <p class="mt-2 mb-0">{{ $action['comments'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Approval Actions -->
                                @if($changeOrder->approvalRequest->isPending() && $changeOrder->approvalRequest->current_approver_id == session('user_id'))
                                    <hr>
                                    <div class="approval-actions">
                                        <h6>Your Approval Required</h6>
                                        <form method="POST" action="{{ route('admin.budget-change-orders.approve', ['projectId' => $changeOrder->bco_project_ms, 'id' => $changeOrder->bco_id]) }}" class="mb-2">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label">Comments (Optional)</label>
                                                <textarea name="comments" class="form-control" rows="3"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.budget-change-orders.reject', ['projectId' => $changeOrder->bco_project_ms, 'id' => $changeOrder->bco_id]) }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                                <textarea name="comments" class="form-control" rows="3" required></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding: 20px 0;
}
.timeline-item {
    position: relative;
    padding-left: 40px;
    margin-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
}
.timeline-content {
    border-left: 2px solid #dee2e6;
    padding-left: 20px;
    padding-bottom: 20px;
}
</style>
@endpush
@endsection
