@extends('layouts.admin')

@section('title', 'Budget vs Actual Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Budget vs Actual Report</h3>
                    <div class="card-tools">
                        @if($selectedProjectId && $reportData)
                        <form action="{{ route('admin.reports.budget-vs-actual.queue-export') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ $selectedProjectId }}">
                            <input type="hidden" name="format" value="csv">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-clock"></i> Queue CSV Export
                            </button>
                        </form>
                        <a href="{{ route('admin.reports.budget-vs-actual.export', ['project_id' => $selectedProjectId, 'format' => 'excel']) }}" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                        <a href="{{ route('admin.reports.budget-vs-actual.export', ['project_id' => $selectedProjectId, 'format' => 'pdf']) }}" 
                           class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Project Selection -->
                    <form method="GET" action="{{ route('admin.reports.budget-vs-actual') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="project_id">Select Project</label>
                                    <select name="project_id" id="project_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">-- Select a Project --</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->proj_id }}" {{ $selectedProjectId == $project->proj_id ? 'selected' : '' }}>
                                                {{ $project->proj_number }} - {{ $project->proj_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($selectedProjectId && $reportData)
                        @if(isset($recentExports) && $recentExports->isNotEmpty())
                        <div class="card card-outline card-primary mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Queued Exports</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Status</th>
                                                <th>Queued</th>
                                                <th>Completed</th>
                                                <th>File</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentExports as $export)
                                            <tr>
                                                <td>#{{ $export->report_export_id }}</td>
                                                <td>
                                                    @if($export->status === 'completed')
                                                        <span class="badge bg-success">Completed</span>
                                                    @elseif($export->status === 'failed')
                                                        <span class="badge bg-danger">Failed</span>
                                                    @elseif($export->status === 'processing')
                                                        <span class="badge bg-warning">Processing</span>
                                                    @else
                                                        <span class="badge bg-secondary">Pending</span>
                                                    @endif
                                                </td>
                                                <td>{{ optional($export->queued_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                                <td>{{ optional($export->completed_at)->format('Y-m-d H:i') ?? '-' }}</td>
                                                <td>{{ $export->file_name ?? '-' }}</td>
                                                <td>
                                                    @if($export->status === 'completed')
                                                        <a href="{{ route('admin.reports.budget-vs-actual.exports.download', $export->report_export_id) }}" class="btn btn-xs btn-success">
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                    @elseif($export->status === 'failed' && $export->error_message)
                                                        <span class="text-danger small">{{ \Illuminate\Support\Str::limit($export->error_message, 90) }}</span>
                                                    @else
                                                        <span class="text-muted small">In queue</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Summary Cards -->
                        @if($summary)
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>${{ number_format($summary['total_revised'], 0) }}</h3>
                                        <p>Total Budget</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>${{ number_format($summary['total_committed'], 0) }}</h3>
                                        <p>Committed (POs)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>${{ number_format($summary['total_actual'], 0) }}</h3>
                                        <p>Actual Spend</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box {{ $summary['total_variance'] >= 0 ? 'bg-primary' : 'bg-danger' }}">
                                    <div class="inner">
                                        <h3>${{ number_format(abs($summary['total_variance']), 0) }}</h3>
                                        <p>{{ $summary['total_variance'] >= 0 ? 'Remaining' : 'Over Budget' }}</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Utilization Summary -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Budget Status Summary</h5>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="info-box bg-success">
                                                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">On Track (&lt;75%)</span>
                                                        <span class="info-box-number">{{ $summary['on_track_count'] }} Cost Codes</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box bg-warning">
                                                    <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">At Risk (75-99%)</span>
                                                        <span class="info-box-number">{{ $summary['at_risk_count'] }} Cost Codes</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-box bg-danger">
                                                    <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                                                    <div class="info-box-content">
                                                        <span class="info-box-text">Over Budget (≥100%)</span>
                                                        <span class="info-box-number">{{ $summary['over_budget_count'] }} Cost Codes</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Overall Utilization Bar -->
                                        <div class="progress-group">
                                            <span class="progress-text">Overall Budget Utilization</span>
                                            <span class="progress-number">{{ number_format($summary['overall_utilization'], 1) }}%</span>
                                            <div class="progress">
                                                <div class="progress-bar {{ $summary['overall_utilization'] >= 90 ? 'bg-danger' : ($summary['overall_utilization'] >= 75 ? 'bg-warning' : 'bg-success') }}" 
                                                     style="width: {{ min($summary['overall_utilization'], 100) }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Detailed Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="budget-report-table">
                                <thead>
                                    <tr>
                                        <th>Cost Code</th>
                                        <th>Description</th>
                                        <th class="text-right">Original Budget</th>
                                        <th class="text-right">Revised Budget</th>
                                        <th class="text-right">Committed</th>
                                        <th class="text-right">Actual</th>
                                        <th class="text-right">Total Spent</th>
                                        <th class="text-right">Variance</th>
                                        <th class="text-center">Utilization</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reportData as $row)
                                    <tr class="cost-code-level-{{ $row->level }}">
                                        <td>
                                            <a href="{{ route('admin.reports.budget-drilldown', [$selectedProjectId, $row->cc_id]) }}">
                                                {{ $row->cost_code }}
                                            </a>
                                        </td>
                                        <td>{{ $row->cost_code_name }}</td>
                                        <td class="text-right">${{ number_format($row->original, 2) }}</td>
                                        <td class="text-right"><strong>${{ number_format($row->revised, 2) }}</strong></td>
                                        <td class="text-right">${{ number_format($row->committed, 2) }}</td>
                                        <td class="text-right">${{ number_format($row->actual, 2) }}</td>
                                        <td class="text-right"><strong>${{ number_format($row->total_spent, 2) }}</strong></td>
                                        <td class="text-right {{ $row->variance < 0 ? 'text-danger' : 'text-success' }}">
                                            <strong>{{ $row->variance >= 0 ? '$' : '($' }}{{ number_format(abs($row->variance), 2) }}{{ $row->variance < 0 ? ')' : '' }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress">
                                                <div class="progress-bar bg-{{ $row->status_level }}" 
                                                     style="width: {{ min($row->utilization_pct, 100) }}%">
                                                    {{ number_format($row->utilization_pct, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($row->status_level == 'danger')
                                                <span class="badge bg-danger">Over Budget</span>
                                            @elseif($row->status_level == 'warning')
                                                <span class="badge bg-warning">At Risk</span>
                                            @else
                                                <span class="badge bg-success">On Track</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Please select a project to view the budget vs actual report.
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
    $('#budget-report-table').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [[0, 'asc']]
    });
});
</script>
@endpush

@push('styles')
<style>
.cost-code-level-1 {
    font-weight: bold;
    background-color: #f8f9fa;
}
.cost-code-level-2 {
    padding-left: 20px;
    background-color: #ffffff;
}
.cost-code-level-3 {
    padding-left: 40px;
    font-size: 0.95em;
}
.progress {
    height: 20px;
    margin-bottom: 0;
}
.progress-bar {
    line-height: 20px;
    font-size: 12px;
}
</style>
@endpush
