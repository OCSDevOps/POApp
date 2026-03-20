@extends('layouts.admin')

@section('title', 'Variance Analysis Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Budget Variance Analysis</h3>
                </div>
                <div class="card-body">
                    <!-- Project Selection -->
                    <form method="GET" action="{{ route('admin.reports.variance-analysis') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="project_id">Select Project</label>
                                    <select name="project_id" id="project_id" class="form-control" onchange="this.form.submit()">
                                        @foreach($projects as $project)
                                            <option value="{{ $project->proj_id }}" {{ $selectedProjectId == $project->proj_id ? 'selected' : '' }}>
                                                {{ $project->proj_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>

                    @if($topVariances)
                        <!-- Utilization Distribution Chart -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Budget Utilization Distribution</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="utilizationChart" height="80"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Variances Table -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Top 10 Budget Variances</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Cost Code</th>
                                                    <th>Description</th>
                                                    <th class="text-right">Revised Budget</th>
                                                    <th class="text-right">Spent (Committed + Actual)</th>
                                                    <th class="text-right">Variance</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topVariances as $variance)
                                                <tr>
                                                    <td>{{ $variance->cc_no }}</td>
                                                    <td>{{ $variance->cc_description }}</td>
                                                    <td class="text-right">${{ number_format($variance->budget_revised_amount, 2) }}</td>
                                                    <td class="text-right">${{ number_format($variance->committed + $variance->actual, 2) }}</td>
                                                    <td class="text-right {{ $variance->variance < 0 ? 'text-danger' : 'text-success' }}">
                                                        <strong>{{ $variance->variance >= 0 ? '$' : '($' }}{{ number_format(abs($variance->variance), 2) }}{{ $variance->variance < 0 ? ')' : '' }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($variance->variance_status == 'over')
                                                            <span class="badge bg-danger">Over Budget</span>
                                                        @elseif($variance->variance_status == 'exact')
                                                            <span class="badge bg-warning">Exact</span>
                                                        @else
                                                            <span class="badge bg-success">Under Budget</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Budget Alerts -->
                        @if($alertsData && $alertsData->count() > 0)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-warning">
                                        <h5><i class="fas fa-exclamation-triangle"></i> Budget Alerts</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Cost Code</th>
                                                    <th>Description</th>
                                                    <th class="text-right">Utilization</th>
                                                    <th class="text-right">Variance</th>
                                                    <th>Alert Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($alertsData as $alert)
                                                <tr class="{{ $alert->utilization >= 90 ? 'table-danger' : 'table-warning' }}">
                                                    <td>{{ $alert->cc_no }}</td>
                                                    <td>{{ $alert->cc_description }}</td>
                                                    <td class="text-right">{{ number_format($alert->utilization, 1) }}%</td>
                                                    <td class="text-right">
                                                        {{ $alert->variance >= 0 ? '$' : '($' }}{{ number_format(abs($alert->variance), 2) }}{{ $alert->variance < 0 ? ')' : '' }}
                                                    </td>
                                                    <td>
                                                        @if($alert->critical_notification_sent)
                                                            <span class="badge bg-danger"><i class="fas fa-times-circle"></i> Critical (≥90%)</span>
                                                        @elseif($alert->warning_notification_sent)
                                                            <span class="badge bg-warning"><i class="fas fa-exclamation-triangle"></i> Warning (≥75%)</span>
                                                        @elseif($alert->variance < 0)
                                                            <span class="badge bg-danger"><i class="fas fa-ban"></i> Over Budget</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
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
@if($utilizationChart)
var ctx = document.getElementById('utilizationChart').getContext('2d');
var chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Under 50%', '50-74%', '75-89% (Warning)', '90-99% (Critical)', '100%+ (Over)'],
        datasets: [{
            label: 'Number of Cost Codes',
            data: [
                {{ $utilizationChart->under_50 ?? 0 }},
                {{ $utilizationChart->pct_50_74 ?? 0 }},
                {{ $utilizationChart->pct_75_89 ?? 0 }},
                {{ $utilizationChart->pct_90_99 ?? 0 }},
                {{ $utilizationChart->over_100 ?? 0 }}
            ],
            backgroundColor: [
                'rgba(40, 167, 69, 0.7)',   // Green - under 50%
                'rgba(23, 162, 184, 0.7)',  // Blue - 50-74%
                'rgba(255, 193, 7, 0.7)',   // Yellow - 75-89%
                'rgba(255, 152, 0, 0.7)',   // Orange - 90-99%
                'rgba(220, 53, 69, 0.7)'    // Red - 100%+
            ],
            borderColor: [
                'rgba(40, 167, 69, 1)',
                'rgba(23, 162, 184, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(255, 152, 0, 1)',
                'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            title: {
                display: true,
                text: 'Budget Utilization Categories'
            }
        }
    }
});
@endif
</script>
@endpush
