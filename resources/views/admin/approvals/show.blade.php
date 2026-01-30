@extends('layout.master')

@section('title', 'Approval Request - ' . ($approvalRequest->apreq_type == 'BudgetChangeOrder' ? 'BCO' : 'PCO'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Status Banner -->
            <div class="alert 
                @if($approvalRequest->apreq_status == 'approved') alert-success
                @elseif($approvalRequest->apreq_status == 'rejected') alert-danger
                @else alert-warning
                @endif">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check"></i> Approval Request
                            <span class="badge 
                                @if($approvalRequest->apreq_status == 'approved') bg-success
                                @elseif($approvalRequest->apreq_status == 'rejected') bg-danger
                                @else bg-warning
                                @endif">
                                Level {{ $approvalRequest->apreq_level }} - {{ ucfirst($approvalRequest->apreq_status) }}
                            </span>
                        </h5>
                    </div>
                    <div>
                        @if($canOverride && in_array($approvalRequest->apreq_status, ['pending', 'rejected']))
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#overrideModal">
                                <i class="fas fa-exclamation-triangle"></i> Override
                            </button>
                        @endif
                        <a href="{{ route('admin.approvals.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column - Request Details -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                {{ $approvalRequest->apreq_type == 'BudgetChangeOrder' ? 'Budget Change Order' : 'PO Change Order' }} Details
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $approvable = $approvalRequest->approvable;
                            @endphp

                            @if($approvalRequest->apreq_type == 'BudgetChangeOrder')
                                <!-- BCO Details -->
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">BCO Number:</th>
                                        <td><strong>{{ $approvable->bco_number }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Project:</th>
                                        <td>{{ $approvable->project->proj_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Cost Code:</th>
                                        <td>
                                            {{ $approvable->costCode->full_code }}<br>
                                            <small class="text-muted">{{ $approvable->costCode->cost_description }}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>BCO Type:</th>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ str_replace('_', ' ', ucwords($approvable->bco_type)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Previous Budget:</th>
                                        <td>${{ number_format($approvable->bco_previous_budget, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>New Budget:</th>
                                        <td>${{ number_format($approvable->bco_new_budget, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Change Amount:</th>
                                        <td>
                                            <strong class="{{ $approvable->bco_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $approvable->bco_amount >= 0 ? '+' : '' }}${{ number_format(abs($approvable->bco_amount), 2) }}
                                            </strong>
                                        </td>
                                    </tr>
                                </table>

                                <div class="mt-3">
                                    <h6>Reason for Change:</h6>
                                    <p class="text-muted">{{ $approvable->bco_reason }}</p>
                                </div>

                                @if($approvable->bco_type == 'transfer')
                                    <div class="mt-3">
                                        <h6>Transfer Information:</h6>
                                        <p class="text-muted">
                                            <strong>From:</strong> {{ $approvable->fromCostCode->full_code ?? 'N/A' }}<br>
                                            <strong>Amount:</strong> ${{ number_format($approvable->bco_transfer_amount, 2) }}
                                        </p>
                                    </div>
                                @endif

                                <div class="mt-3">
                                    <a href="{{ route('admin.budget-change-orders.show', $approvable->bco_id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt"></i> View Full BCO
                                    </a>
                                </div>

                            @else
                                <!-- PCO Details -->
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">PCO Number:</th>
                                        <td><strong>{{ $approvable->poco_number }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>PO Number:</th>
                                        <td>
                                            <a href="{{ route('admin.porder.show', $approvable->purchaseOrder->porder_id) }}">
                                                {{ $approvable->purchaseOrder->porder_no }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Project:</th>
                                        <td>{{ $approvable->purchaseOrder->project->proj_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Supplier:</th>
                                        <td>{{ $approvable->purchaseOrder->supplier->sup_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>PCO Type:</th>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ str_replace('_', ' ', ucwords($approvable->poco_type)) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Previous PO Total:</th>
                                        <td>${{ number_format($approvable->poco_previous_total, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>New PO Total:</th>
                                        <td>${{ number_format($approvable->poco_new_total, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Change Amount:</th>
                                        <td>
                                            <strong class="{{ $approvable->poco_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $approvable->poco_amount >= 0 ? '+' : '' }}${{ number_format(abs($approvable->poco_amount), 2) }}
                                            </strong>
                                        </td>
                                    </tr>
                                </table>

                                <div class="mt-3">
                                    <h6>Reason for Change:</h6>
                                    <p class="text-muted">{{ $approvable->poco_reason }}</p>
                                </div>

                                <div class="mt-3">
                                    <a href="{{ route('admin.po-change-orders.show', $approvable->poco_id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-external-link-alt"></i> View Full PCO
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Approval Info & Actions -->
                <div class="col-md-6">
                    <!-- Approval Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Approval Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Request Level:</th>
                                    <td>
                                        <span class="badge bg-secondary">Level {{ $approvalRequest->apreq_level }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Requested By:</th>
                                    <td>{{ $approvalRequest->requester->u_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Requested Date:</th>
                                    <td>{{ \Carbon\Carbon::parse($approvalRequest->apreq_created_at)->format('m/d/Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Current Approver:</th>
                                    <td>{{ $approvalRequest->approver->u_name ?? 'Pending Assignment' }}</td>
                                </tr>
                                @if($approvalRequest->apreq_status != 'pending')
                                    <tr>
                                        <th>Processed Date:</th>
                                        <td>{{ \Carbon\Carbon::parse($approvalRequest->apreq_approved_at)->format('m/d/Y g:i A') }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Age:</th>
                                    <td>
                                        @php
                                            $age = now()->diffInHours($approvalRequest->apreq_created_at);
                                            $isOverdue = $age > 48;
                                        @endphp
                                        <span class="badge bg-{{ $isOverdue ? 'danger' : 'warning' }}">
                                            {{ $age }} hours
                                        </span>
                                        @if($isOverdue)
                                            <span class="text-danger ms-2">(Overdue)</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            @if($approvalRequest->workflow)
                                <div class="mt-3">
                                    <h6>Workflow Details:</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Workflow:</strong> {{ $approvalRequest->workflow->apwf_name }}</li>
                                        <li><strong>Total Levels:</strong> {{ $approvalRequest->workflow->apwf_total_levels }}</li>
                                        <li><strong>Progress:</strong> {{ $approvalRequest->apreq_level }} of {{ $approvalRequest->workflow->apwf_total_levels }}</li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Approval Actions -->
                    @if($approvalRequest->apreq_status == 'pending' && $canApprove)
                        <div class="card mb-4">
                            <div class="card-header bg-warning">
                                <h5 class="mb-0">Your Approval Action Required</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.approvals.approve', $approvalRequest->apreq_id) }}" class="mb-3">
                                    @csrf
                                    <h6>Approve Request</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Comments (Optional)</label>
                                        <textarea name="comments" class="form-control" rows="3" 
                                                  placeholder="Add any comments or notes..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>

                                <hr>

                                <form method="POST" action="{{ route('admin.approvals.reject', $approvalRequest->apreq_id) }}">
                                    @csrf
                                    <h6>Reject Request</h6>
                                    <div class="mb-3">
                                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                        <textarea name="comments" class="form-control" rows="3" 
                                                  placeholder="Please explain why you are rejecting this request..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Approval History -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Approval History</h5>
                        </div>
                        <div class="card-body">
                            @if($approvalHistory->isEmpty())
                                <p class="text-muted">No approval history yet.</p>
                            @else
                                <div class="approval-timeline">
                                    @foreach($approvalHistory as $history)
                                        <div class="timeline-item">
                                            <div class="timeline-marker 
                                                @if($history->apah_action == 'approved') bg-success
                                                @elseif($history->apah_action == 'rejected') bg-danger
                                                @elseif($history->apah_action == 'override') bg-warning
                                                @else bg-info
                                                @endif">
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>{{ $history->user->u_name }}</strong>
                                                        <span class="badge bg-{{ 
                                                            $history->apah_action == 'approved' ? 'success' : 
                                                            ($history->apah_action == 'rejected' ? 'danger' : 
                                                            ($history->apah_action == 'override' ? 'warning' : 'info')) 
                                                        }} ms-2">
                                                            {{ ucfirst($history->apah_action) }}
                                                        </span>
                                                        <div class="text-muted small">
                                                            {{ \Carbon\Carbon::parse($history->apah_timestamp)->format('m/d/Y g:i A') }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($history->apah_comments)
                                                    <div class="mt-2">
                                                        <small class="text-muted">{{ $history->apah_comments }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Override Modal -->
<div class="modal fade" id="overrideModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Override Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.approvals.override', $approvalRequest->apreq_id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Warning:</strong> This action will bypass the normal approval workflow and immediately approve the request.
                        This action is logged for audit purposes.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Reason for Override <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="4" 
                                  placeholder="Explain why you are overriding the approval process..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-exclamation-triangle"></i> Override and Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.approval-timeline {
    position: relative;
    padding-left: 40px;
}

.timeline-item {
    position: relative;
    padding-bottom: 30px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -29px;
    top: 20px;
    height: calc(100% + 10px);
    width: 2px;
    background-color: #dee2e6;
}

.timeline-item:last-child::before {
    display: none;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 3px solid #fff;
}

.timeline-content {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}
</style>
@endpush
@endsection
