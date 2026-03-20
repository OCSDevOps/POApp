@extends('layouts.admin')

@section('title', 'Committed vs Actual Report')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Committed vs Actual Timeline
                    </h3>
                    <div class="card-tools">
                        @if($projectId)
                        <a href="{{ route('admin.reports.committed-vs-actual.export', request()->all()) }}" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.reports.committed-vs-actual') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="project_id">Select Project</label>
                                    <select name="project_id" id="project_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">-- Select a Project --</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->proj_id }}" 
                                                {{ $projectId == $project->proj_id ? 'selected' : '' }}>
                                                {{ $project->proj_number }} - {{ $project->proj_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date_range">Date Range</label>
                                    <select name="date_range" id="date_range" class="form-control" onchange="this.form.submit()">
                                        <option value="3months" {{ $dateRange == '3months' ? 'selected' : '' }}>Last 3 Months</option>
                                        <option value="6months" {{ $dateRange == '6months' ? 'selected' : '' }}>Last 6 Months</option>
                                        <option value="1year" {{ $dateRange == '1year' ? 'selected' : '' }}>Last 1 Year</option>
                                        <option value="all" {{ $dateRange == 'all' ? 'selected' : '' }}>All Time</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($projectId && $summary)
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>${{ number_format($summary['total_budget'], 0) }}</h3>
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
                                        <h3>${{ number_format($summary['cumulative_committed'], 0) }}</h3>
                                        <p>Total Committed</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>${{ number_format($summary['cumulative_actual'], 0) }}</h3>
                                        <p>Total Actual</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="small-box {{ $summary['remaining_budget'] >= 0 ? 'bg-primary' : 'bg-danger' }}">
                                    <div class="inner">
                                        <h3>${{ number_format(abs($summary['remaining_budget']), 0) }}</h3>
                                        <p>{{ $summary['remaining_budget'] >= 0 ? 'Remaining' : 'Over Budget' }}</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-piggy-bank"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Period Stats -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>
                                            Period Statistics: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2 text-center">
                                                <h4 class="text-warning">${{ number_format($summary['period_committed'], 0) }}</h4>
                                                <small>Period Committed</small>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <h4 class="text-success">${{ number_format($summary['period_actual'], 0) }}</h4>
                                                <small>Period Actual</small>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <h4 class="text-info">${{ number_format($summary['variance'], 0) }}</h4>
                                                <small>Period Variance</small>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <h4>{{ $summary['po_count'] }}</h4>
                                                <small>POs Created</small>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <h4>{{ $summary['ro_count'] }}</h4>
                                                <small>Receipts</small>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <h4 class="{{ $summary['utilization_rate'] >= 90 ? 'text-danger' : ($summary['utilization_rate'] >= 75 ? 'text-warning' : 'text-success') }}">
                                                    {{ number_format($summary['utilization_rate'], 1) }}%
                                                </h4>
                                                <small>Utilization Rate</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Chart -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Monthly Committed vs Actual</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="timelineChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cumulative Chart -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Cumulative Spending Trend</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="cumulativeChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cost Code Breakdown -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Cost Code Breakdown</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped table-hover" id="costcode-table">
                                                <thead>
                                                    <tr>
                                                        <th>Cost Code</th>
                                                        <th>Description</th>
                                                        <th class="text-right">Budget</th>
                                                        <th class="text-right">Committed</th>
                                                        <th class="text-right">Actual</th>
                                                        <th class="text-right">Total Spent</th>
                                                        <th class="text-right">Variance</th>
                                                        <th class="text-center">Utilization</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($costCodeBreakdown as $row)
                                                    @php
                                                        $totalSpent = ($row->committed ?? 0) + ($row->actual ?? 0);
                                                        $variance = $row->budget - $totalSpent;
                                                        $utilization = $row->budget > 0 ? ($totalSpent / $row->budget * 100) : 0;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $row->cc_no }}</td>
                                                        <td>{{ $row->cc_description }}</td>
                                                        <td class="text-right">${{ number_format($row->budget, 2) }}</td>
                                                        <td class="text-right">${{ number_format($row->committed ?? 0, 2) }}</td>
                                                        <td class="text-right">${{ number_format($row->actual ?? 0, 2) }}</td>
                                                        <td class="text-right">${{ number_format($totalSpent, 2) }}</td>
                                                        <td class="text-right {{ $variance < 0 ? 'text-danger' : 'text-success' }}">
                                                            ${{ number_format($variance, 2) }}
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="progress">
                                                                <div class="progress-bar {{ $utilization >= 90 ? 'bg-danger' : ($utilization >= 75 ? 'bg-warning' : 'bg-success') }}" 
                                                                     style="width: {{ min($utilization, 100) }}%">
                                                                    {{ number_format($utilization, 1) }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Please select a project to view the Committed vs Actual report.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
@if($timelineData)
    // Monthly Committed vs Actual Chart
    var ctx1 = document.getElementById('timelineChart').getContext('2d');
    var timelineChart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: {!! json_encode($timelineData['labels']) !!},
            datasets: [
                {
                    label: 'Committed (POs)',
                    data: {!! json_encode($timelineData['committed']) !!},
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: 'rgba(255, 193, 7, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Actual (Receipts)',
                    data: {!! json_encode($timelineData['actual']) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Spending Comparison'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.raw.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Calculate cumulative data
    var committedData = {!! json_encode($timelineData['committed']) !!};
    var actualData = {!! json_encode($timelineData['actual']) !!};
    var cumulativeCommitted = [];
    var cumulativeActual = [];
    var runningCommitted = 0;
    var runningActual = 0;
    
    for (var i = 0; i < committedData.length; i++) {
        runningCommitted += committedData[i];
        runningActual += actualData[i];
        cumulativeCommitted.push(runningCommitted);
        cumulativeActual.push(runningActual);
    }

    // Cumulative Trend Chart
    var ctx2 = document.getElementById('cumulativeChart').getContext('2d');
    var cumulativeChart = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: {!! json_encode($timelineData['labels']) !!},
            datasets: [
                {
                    label: 'Cumulative Committed',
                    data: cumulativeCommitted,
                    borderColor: 'rgba(255, 193, 7, 1)',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Cumulative Actual',
                    data: cumulativeActual,
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Cumulative Spending Trend'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.raw.toLocaleString();
                        }
                    }
                }
            }
        }
    });
@endif

$(document).ready(function() {
    $('#costcode-table').DataTable({
        "paging": true,
        "pageLength": 25,
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
.progress {
    height: 20px;
    margin-bottom: 0;
}
.progress-bar {
    line-height: 20px;
    font-size: 11px;
}
.small-box {
    border-radius: 4px;
}
.small-box .icon {
    font-size: 50px;
}
</style>
@endpush
