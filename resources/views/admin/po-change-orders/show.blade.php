@extends('layout.master')

@section('title', 'PO Change Order - ' . $changeOrder->poco_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Status Banner -->
            <div class="alert 
                @if($changeOrder->poco_status == 'approved') alert-success
                @elseif($changeOrder->poco_status == 'rejected') alert-danger
                @elseif($changeOrder->poco_status == 'pending_approval') alert-warning
                @elseif($changeOrder->poco_status == 'cancelled') alert-secondary
                @else alert-info
                @endif">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice-dollar"></i> {{ $changeOrder->poco_number }}
                            <span class="badge 
                                @if($changeOrder->poco_status == 'approved') bg-success
                                @elseif($changeOrder->poco_status == 'rejected') bg-danger
                                @elseif($changeOrder->poco_status == 'pending_approval') bg-warning
                                @elseif($changeOrder->poco_status == 'cancelled') bg-secondary
                                @else bg-info
                                @endif">
                                {{ str_replace('_', ' ', ucfirst($changeOrder->poco_status)) }}
                            </span>
                        </h5>
                    </div>
                    <div>
                        @if($changeOrder->poco_status == 'draft')
                            <form method="POST" action="{{ route('admin.po-change-orders.submit', $changeOrder->poco_id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success me-2">
                                    <i class="fas fa-paper-plane"></i> Submit for Approval
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.po-change-orders.cancel', $changeOrder->poco_id) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-times"></i> Cancel PCO
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.porder.show', $changeOrder->poco_po_ms) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to PO
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column - PCO Details -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">PO Change Order Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">PCO Number:</th>
                                    <td><strong>{{ $changeOrder->poco_number }}</strong></td>
                                </tr>
                                <tr>
                                    <th>PO Number:</th>
                                    <td>
                                        <a href="{{ route('admin.porder.show', $changeOrder->purchaseOrder->porder_id) }}">
                                            {{ $changeOrder->purchaseOrder->porder_no }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Project:</th>
                                    <td>{{ $changeOrder->purchaseOrder->project->proj_name }}</td>
                                </tr>
                                <tr>
                                    <th>Supplier:</th>
                                    <td>{{ $changeOrder->purchaseOrder->supplier->sup_name }}</td>
                                </tr>
                                <tr>
                                    <th>PCO Type:</th>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ str_replace('_', ' ', ucwords($changeOrder->poco_type)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created By:</th>
                                    <td>{{ $changeOrder->creator->u_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created Date:</th>
                                    <td>{{ $changeOrder->created_at->format('m/d/Y g:i A') }}</td>
                                </tr>
                                @if($changeOrder->poco_status == 'approved')
                                    <tr>
                                        <th>Approved By:</th>
                                        <td>{{ $changeOrder->approver->u_name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Approved Date:</th>
                                        <td>{{ $changeOrder->poco_approved_at ? \Carbon\Carbon::parse($changeOrder->poco_approved_at)->format('m/d/Y g:i A') : 'N/A' }}</td>
                                    </tr>
                                @endif
                            </table>

                            <!-- Reason for Change -->
                            <div class="mt-3">
                                <h6>Reason for Change:</h6>
                                <p class="text-muted">{{ $changeOrder->poco_reason }}</p>
                            </div>

                            <!-- Additional Details -->
                            @if($changeOrder->poco_details)
                                @php
                                    $details = json_decode($changeOrder->poco_details, true);
                                @endphp
                                <div class="mt-3">
                                    <h6>Additional Details:</h6>
                                    @if($changeOrder->poco_type == 'item_change' && isset($details['item_details']))
                                        <p class="text-muted">{{ $details['item_details'] }}</p>
                                    @elseif($changeOrder->poco_type == 'date_change')
                                        <p class="text-muted">
                                            <strong>Old Date:</strong> {{ $details['old_delivery_date'] ?? 'N/A' }}<br>
                                            <strong>New Date:</strong> {{ $details['new_delivery_date'] ?? 'N/A' }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Financial Impact -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Financial Impact</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block">Previous PO Total</small>
                                        <h4 class="mb-0">${{ number_format($changeOrder->poco_previous_total, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 rounded" style="background-color: #f0f9ff;">
                                        <small class="text-muted d-block">New PO Total</small>
                                        <h4 class="mb-0">${{ number_format($changeOrder->poco_new_total, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 rounded {{ $changeOrder->poco_amount >= 0 ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' }}">
                                        <small class="text-muted d-block">Change Amount</small>
                                        <h4 class="mb-0 {{ $changeOrder->poco_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $changeOrder->poco_amount >= 0 ? '+' : '' }}${{ number_format(abs($changeOrder->poco_amount), 2) }}
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <!-- Budget Impact (if available) -->
                            @if(isset($budgetInfo))
                                <div class="mt-4">
                                    <h6>Budget Impact:</h6>
                                    <div class="alert alert-{{ $budgetInfo['utilization_after'] >= 90 ? 'danger' : ($budgetInfo['utilization_after'] >= 75 ? 'warning' : 'info') }}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small>Current Budget Utilization</small>
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: {{ $budgetInfo['utilization_before'] }}%"
                                                         aria-valuenow="{{ $budgetInfo['utilization_before'] }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        {{ number_format($budgetInfo['utilization_before'], 1) }}%
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <small>After PCO Approval</small>
                                                <div class="progress" style="height: 25px;">
                                                    <div class="progress-bar bg-{{ $budgetInfo['utilization_after'] >= 90 ? 'danger' : ($budgetInfo['utilization_after'] >= 75 ? 'warning' : 'success') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $budgetInfo['utilization_after'] }}%"
                                                         aria-valuenow="{{ $budgetInfo['utilization_after'] }}" 
                                                         aria-valuemin="0" aria-valuemax="100">
                                                        {{ number_format($budgetInfo['utilization_after'], 1) }}%
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Approval Status -->
                    @if($changeOrder->approvalRequest)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Approval Workflow</h5>
                            </div>
                            <div class="card-body">
                                <div class="approval-timeline">
                                    @foreach($approvalHistory as $approval)
                                        <div class="timeline-item">
                                            <div class="timeline-marker 
                                                @if($approval->apah_action == 'approved') bg-success
                                                @elseif($approval->apah_action == 'rejected') bg-danger
                                                @else bg-warning
                                                @endif">
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>{{ $approval->user->u_name }}</strong>
                                                        <span class="badge bg-{{ $approval->apah_action == 'approved' ? 'success' : ($approval->apah_action == 'rejected' ? 'danger' : 'warning') }} ms-2">
                                                            {{ ucfirst($approval->apah_action) }}
                                                        </span>
                                                        <div class="text-muted small">
                                                            {{ \Carbon\Carbon::parse($approval->apah_timestamp)->format('m/d/Y g:i A') }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($approval->apah_comments)
                                                    <div class="mt-2">
                                                        <small class="text-muted">{{ $approval->apah_comments }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Approval Actions (if pending and user is approver) -->
                                @if($changeOrder->poco_status == 'pending_approval' && $canApprove)
                                    <div class="mt-4 p-3 bg-light rounded">
                                        <h6>Your Approval Action:</h6>
                                        <form method="POST" action="{{ route('admin.approvals.approve', $changeOrder->approvalRequest->apreq_id) }}" class="mb-2">
                                            @csrf
                                            <div class="mb-2">
                                                <textarea name="comments" class="form-control" rows="2" 
                                                          placeholder="Optional comments..."></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-success me-2">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.approvals.reject', $changeOrder->approvalRequest->apreq_id) }}">
                                            @csrf
                                            <div class="mb-2">
                                                <textarea name="comments" class="form-control" rows="2" 
                                                          placeholder="Reason for rejection (required)..." required></textarea>
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
