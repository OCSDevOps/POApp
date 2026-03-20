@extends('layouts.admin')

@section('title', 'CCO ' . $changeOrder->cco_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
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

            <!-- Header with CCO number and status -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="fas fa-exchange-alt"></i> {{ $changeOrder->cco_number }}
                    <span class="badge
                        @if($changeOrder->cco_status == 'draft') bg-secondary
                        @elseif($changeOrder->cco_status == 'pending_approval') bg-warning
                        @elseif($changeOrder->cco_status == 'approved') bg-success
                        @elseif($changeOrder->cco_status == 'rejected') bg-danger
                        @elseif($changeOrder->cco_status == 'cancelled') bg-dark
                        @endif">
                        {{ str_replace('_', ' ', ucfirst($changeOrder->cco_status)) }}
                    </span>
                </h4>
                <div>
                    @if($changeOrder->cco_status == 'draft')
                        <form method="POST" action="{{ route('admin.contract-change-orders.submit', $changeOrder->cco_id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i> Submit for Approval
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.contract-change-orders.cancel', $changeOrder->cco_id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this change order?')">
                                <i class="fas fa-ban"></i> Cancel
                            </button>
                        </form>
                    @endif

                    @if($changeOrder->cco_status == 'pending_approval')
                        <form method="POST" action="{{ route('admin.contract-change-orders.approve', $changeOrder->cco_id) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    @endif

                    <a href="{{ route('admin.contract-change-orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Amount Highlight -->
                <div class="col-md-12 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-4">
                            <small class="text-muted d-block mb-1">Change Order Amount</small>
                            <h2 class="mb-0 {{ $changeOrder->cco_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $changeOrder->cco_amount >= 0 ? '+' : '' }}${{ number_format(abs($changeOrder->cco_amount), 2) }}
                            </h2>
                        </div>
                    </div>
                </div>

                <!-- Details Card -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Change Order Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Contract #:</th>
                                    <td>
                                        <a href="{{ route('admin.contracts.show', $changeOrder->contract->contract_id) }}">
                                            {{ $changeOrder->contract->contract_number }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Project:</th>
                                    <td>{{ $changeOrder->contract->project->proj_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Subcontractor:</th>
                                    <td>{{ $changeOrder->contract->supplier->sup_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $changeOrder->cco_description }}</td>
                                </tr>
                                @if($changeOrder->cco_reason)
                                    <tr>
                                        <th>Reason:</th>
                                        <td>{{ $changeOrder->cco_reason }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Created By:</th>
                                    <td>{{ $changeOrder->creator->name ?? $changeOrder->creator->user_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Created Date:</th>
                                    <td>{{ $changeOrder->created_at->format('m/d/Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Approval History -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Approval History</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($approvalHistory['history']) && count($approvalHistory['history']) > 0)
                                <div class="approval-timeline">
                                    @foreach($approvalHistory['history'] as $entry)
                                        <div class="timeline-item">
                                            <div class="timeline-marker
                                                @if($entry['action'] == 'approved' || $entry['action'] == 'approve') bg-success
                                                @elseif($entry['action'] == 'rejected' || $entry['action'] == 'reject') bg-danger
                                                @elseif($entry['action'] == 'submitted') bg-info
                                                @elseif($entry['action'] == 'cancelled') bg-dark
                                                @else bg-warning
                                                @endif">
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>{{ $entry['user_name'] }}</strong>
                                                        <span class="badge
                                                            @if($entry['action'] == 'approved' || $entry['action'] == 'approve') bg-success
                                                            @elseif($entry['action'] == 'rejected' || $entry['action'] == 'reject') bg-danger
                                                            @elseif($entry['action'] == 'submitted') bg-info
                                                            @elseif($entry['action'] == 'cancelled') bg-dark
                                                            @else bg-warning
                                                            @endif ms-2">
                                                            {{ ucfirst($entry['action']) }}
                                                        </span>
                                                        <div class="text-muted small">
                                                            {{ \Carbon\Carbon::parse($entry['timestamp'])->format('m/d/Y g:i A') }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @if(!empty($entry['comments']))
                                                    <div class="mt-2">
                                                        <small class="text-muted">{{ $entry['comments'] }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">No approval history yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
@if($changeOrder->cco_status == 'pending_approval')
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.contract-change-orders.reject', $changeOrder->cco_id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Reject Change Order</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="reason" id="rejection_reason" class="form-control" rows="4"
                                      required placeholder="Please provide a reason for rejecting this change order..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Reject Change Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@push('styles')
<style>
.approval-timeline {
    position: relative;
    padding-left: 40px;
}

.timeline-item {
    position: relative;
    padding-bottom: 25px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -29px;
    top: 20px;
    height: calc(100% + 5px);
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
    padding: 12px;
    border-radius: 8px;
}
</style>
@endpush
@endsection
