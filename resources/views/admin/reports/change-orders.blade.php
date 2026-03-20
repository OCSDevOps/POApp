@extends('layouts.admin')

@section('title', 'Change Orders Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-exchange-alt"></i> Change Orders Report
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.reports.change-orders.export', request()->all()) }}" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.reports.change-orders') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="project_id">Project</label>
                                    <select name="project_id" id="project_id" class="form-control">
                                        <option value="">All Projects</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->proj_id }}" 
                                                {{ $filters['project_id'] == $project->proj_id ? 'selected' : '' }}>
                                                {{ $project->proj_number }} - {{ $project->proj_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="co_type">CO Type</label>
                                    <select name="co_type" id="co_type" class="form-control">
                                        <option value="all" {{ $filters['co_type'] == 'all' ? 'selected' : '' }}>All Types</option>
                                        <option value="budget" {{ $filters['co_type'] == 'budget' ? 'selected' : '' }}>Budget CO Only</option>
                                        <option value="po" {{ $filters['co_type'] == 'po' ? 'selected' : '' }}>PO CO Only</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>All Status</option>
                                        <option value="draft" {{ $filters['status'] == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="pending_approval" {{ $filters['status'] == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                                        <option value="approved" {{ $filters['status'] == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $filters['status'] == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="cancelled" {{ $filters['status'] == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_from">Date From</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control" 
                                           value="{{ $filters['date_from'] }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date_to">Date To</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control" 
                                           value="{{ $filters['date_to'] }}">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($summary)
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ $summary['total_count'] }}</h3>
                                        <p>Total COs</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-exchange-alt"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>${{ number_format($summary['total_increase'], 0) }}</h3>
                                        <p>Total Increases</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-arrow-up"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>${{ number_format($summary['total_decrease'], 0) }}</h3>
                                        <p>Total Decreases</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-arrow-down"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="small-box {{ $summary['net_change'] >= 0 ? 'bg-primary' : 'bg-warning' }}">
                                    <div class="inner">
                                        <h3>${{ number_format(abs($summary['net_change']), 0) }}</h3>
                                        <p>Net Change</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-calculator"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ $summary['approved_count'] }}</h3>
                                        <p>Approved</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ $summary['pending_count'] }}</h3>
                                        <p>Pending</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Breakdown -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Status Breakdown</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="progress-group">
                                                    <span class="progress-text">Approved</span>
                                                    <span class="float-end"><b>{{ $summary['approved_count'] }}</b>/{{ $summary['total_count'] }}</span>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-success" style="width: {{ $summary['total_count'] > 0 ? ($summary['approved_count'] / $summary['total_count'] * 100) : 0 }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="progress-group">
                                                    <span class="progress-text">Pending</span>
                                                    <span class="float-end"><b>{{ $summary['pending_count'] }}</b>/{{ $summary['total_count'] }}</span>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-warning" style="width: {{ $summary['total_count'] > 0 ? ($summary['pending_count'] / $summary['total_count'] * 100) : 0 }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="progress-group">
                                                    <span class="progress-text">Rejected</span>
                                                    <span class="float-end"><b>{{ $summary['rejected_count'] }}</b>/{{ $summary['total_count'] }}</span>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-danger" style="width: {{ $summary['total_count'] > 0 ? ($summary['rejected_count'] / $summary['total_count'] * 100) : 0 }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="progress-group">
                                                    <span class="progress-text">Draft</span>
                                                    <span class="float-end"><b>{{ $summary['draft_count'] }}</b>/{{ $summary['total_count'] }}</span>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-secondary" style="width: {{ $summary['total_count'] > 0 ? ($summary['draft_count'] / $summary['total_count'] * 100) : 0 }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Change Orders Table -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Change Orders Detail</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-hover" id="change-orders-table">
                                                <thead>
                                                    <tr>
                                                        <th>Type</th>
                                                        <th>CO Number</th>
                                                        <th>Project</th>
                                                        <th>Cost Code</th>
                                                        <th>Change Type</th>
                                                        <th class="text-right">Amount</th>
                                                        <th class="text-right">Previous</th>
                                                        <th class="text-right">New</th>
                                                        <th>Status</th>
                                                        <th>Created By</th>
                                                        <th>Created Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $allOrders = $budgetChangeOrders->merge($poChangeOrders)->sortByDesc('created_at');
                                                    @endphp
                                                    @forelse($allOrders as $order)
                                                    <tr>
                                                        <td>
                                                            @if($order->co_type === 'budget')
                                                                <span class="badge bg-info">Budget CO</span>
                                                            @else
                                                                <span class="badge bg-primary">PO CO</span>
                                                            @endif
                                                        </td>
                                                        <td><strong>{{ $order->number }}</strong></td>
                                                        <td>{{ $order->project_name }}</td>
                                                        <td>{{ $order->cost_code ?? 'N/A' }}</td>
                                                        <td>{{ ucwords(str_replace('_', ' ', $order->type)) }}</td>
                                                        <td class="text-right {{ $order->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                            <strong>
                                                                {{ $order->amount >= 0 ? '+' : '-' }}${{ number_format(abs($order->amount), 2) }}
                                                            </strong>
                                                        </td>
                                                        <td class="text-right">${{ number_format($order->previous_budget, 2) }}</td>
                                                        <td class="text-right">${{ number_format($order->new_budget, 2) }}</td>
                                                        <td class="text-center">
                                                            @switch($order->status)
                                                                @case('approved')
                                                                    <span class="badge bg-success">Approved</span>
                                                                    @break
                                                                @case('pending_approval')
                                                                    <span class="badge bg-warning">Pending</span>
                                                                    @break
                                                                @case('rejected')
                                                                    <span class="badge bg-danger">Rejected</span>
                                                                    @break
                                                                @case('draft')
                                                                    <span class="badge bg-secondary">Draft</span>
                                                                    @break
                                                                @case('cancelled')
                                                                    <span class="badge bg-dark">Cancelled</span>
                                                                    @break
                                                                @default
                                                                    <span class="badge bg-light">{{ ucwords($order->status) }}</span>
                                                            @endswitch
                                                        </td>
                                                        <td>{{ $order->created_by_name ?? 'N/A' }}</td>
                                                        <td>{{ date('m/d/Y', strtotime($order->created_at)) }}</td>
                                                        <td class="text-center">
                                                            @if($order->co_type === 'budget')
                                                                <a href="{{ route('admin.budget-change-orders.show', [$order->project_name, $order->id]) }}" 
                                                                   class="btn btn-sm btn-info" target="_blank">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @else
                                                                <a href="{{ route('admin.po-change-orders.show', $order->id) }}" 
                                                                   class="btn btn-sm btn-info" target="_blank">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="12" class="text-center">
                                                            <div class="alert alert-info mb-0">
                                                                <i class="fas fa-info-circle"></i> No change orders found for the selected filters.
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Please select filters and click search to view the change orders report.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#change-orders-table').DataTable({
        "paging": true,
        "pageLength": 25,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [[10, 'desc']], // Sort by created date desc
        "columnDefs": [
            { "orderable": false, "targets": [11] } // Disable sorting on actions column
        ]
    });
});
</script>
@endpush

@push('styles')
<style>
.progress-group {
    margin-bottom: 1rem;
}
.progress-group .progress-text {
    font-weight: 600;
}
.progress-group .progress-number {
    float: right;
}
.progress {
    height: 10px;
    margin-top: 5px;
}
</style>
@endpush
