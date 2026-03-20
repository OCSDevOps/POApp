@extends('layouts.admin')

@section('title', 'Project Scheduling')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 font-weight-bold text-gray-800">
                <i class="fas fa-calendar-alt me-2 text-primary"></i>Project Scheduling
            </h4>
            <p class="text-muted mb-0">CPM schedule status across all projects. View activities, critical paths, and health grades.</p>
        </div>
        <div>
            <a href="{{ route('admin.schedule.calendars') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-clock me-1"></i> Work Calendars
            </a>
        </div>
    </div>

    {{-- Summary Stat Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Projects</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projects->count() }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Scheduled</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projects->filter(fn($p) => ($p->latest_run ?? null) !== null)->count() }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Critical Tasks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projects->sum(fn($p) => $p->critical_count ?? 0) }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Needs Attention</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projects->filter(fn($p) => in_array($p->health_grade ?? '', ['D', 'F']))->count() }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-heart-broken"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Projects Schedule Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-1"></i> Project Schedule Overview
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="scheduleTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Project</th>
                            <th class="text-center">Activities</th>
                            <th class="text-center">Last Calculated</th>
                            <th class="text-center">Health Grade</th>
                            <th class="text-center">Critical Tasks</th>
                            <th class="text-center" style="min-width: 160px;">Completion %</th>
                            <th class="text-center" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $index => $project)
                            @php
                                $latestRun = $project->latest_run ?? null;
                                $activityCount = $project->activity_count ?? 0;
                                $criticalCount = $project->critical_count ?? 0;
                                $completionPct = $project->completion_pct ?? 0;
                                $healthGrade = $project->health_grade ?? null;

                                // Health grade badge color
                                $gradeColors = [
                                    'A' => 'success',
                                    'B' => 'primary',
                                    'C' => 'warning',
                                    'D' => 'danger',
                                    'F' => 'danger',
                                ];
                                $gradeBg = $gradeColors[$healthGrade] ?? 'secondary';

                                // Progress bar color
                                if ($completionPct >= 75) {
                                    $progressColor = 'bg-success';
                                } elseif ($completionPct >= 40) {
                                    $progressColor = 'bg-info';
                                } elseif ($completionPct > 0) {
                                    $progressColor = 'bg-warning';
                                } else {
                                    $progressColor = 'bg-secondary';
                                }
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.schedule.show', $project->proj_id) }}" class="text-decoration-none fw-semibold">
                                        {{ $project->proj_name }}
                                    </a>
                                    @if($project->proj_number)
                                        <br><small class="text-muted">{{ $project->proj_number }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $activityCount }}</span>
                                </td>
                                <td class="text-center">
                                    @if($latestRun)
                                        <span title="{{ $latestRun->run_created_at->format('m/d/Y h:i A') }}">
                                            {{ $latestRun->run_created_at->format('m/d/Y') }}
                                        </span>
                                    @else
                                        <span class="text-muted fst-italic">Never</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($healthGrade)
                                        <span class="badge bg-{{ $gradeBg }} fs-6 px-3">{{ $healthGrade }}</span>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($criticalCount > 0)
                                        <span class="badge bg-danger">{{ $criticalCount }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                            <div class="progress-bar {{ $progressColor }}"
                                                 role="progressbar"
                                                 style="width: {{ $completionPct }}%"
                                                 aria-valuenow="{{ $completionPct }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                                @if($completionPct >= 15)
                                                    {{ number_format($completionPct, 0) }}%
                                                @endif
                                            </div>
                                        </div>
                                        @if($completionPct < 15)
                                            <small class="text-muted">{{ number_format($completionPct, 0) }}%</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.schedule.show', $project->proj_id) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="View Schedule">
                                        <i class="fas fa-calendar-check"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                                    No projects found. Create a project first to begin scheduling.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#scheduleTable').DataTable({
        "paging": true,
        "pageLength": 25,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "order": [[1, 'asc']],
        "columnDefs": [
            { "orderable": false, "targets": [0, 7] },
            { "searchable": false, "targets": [0, 7] }
        ],
        "language": {
            "emptyTable": "No projects with schedule data found.",
            "zeroRecords": "No matching projects found."
        }
    });
});
</script>
@endpush
