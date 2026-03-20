@extends('layouts.admin')

@section('title', 'Schedule: ' . $project->proj_name)

@push('styles')
<style>
    /* ===== Gantt Container ===== */
    .gantt-container {
        display: flex;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        background: #fff;
        overflow: hidden;
        position: relative;
        min-height: 400px;
    }

    /* ===== Left Panel (Activity List) ===== */
    .gantt-left-panel {
        width: 420px;
        min-width: 420px;
        border-right: 2px solid #d1d3e2;
        overflow-y: auto;
        overflow-x: hidden;
        flex-shrink: 0;
        z-index: 2;
        background: #fff;
    }

    .gantt-left-header {
        display: flex;
        align-items: center;
        height: 50px;
        background: #f8f9fc;
        border-bottom: 2px solid #d1d3e2;
        font-weight: 600;
        font-size: 0.8rem;
        color: #5a5c69;
        position: sticky;
        top: 0;
        z-index: 3;
    }

    .gantt-left-header .gl-col {
        padding: 0 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .gantt-left-row {
        display: flex;
        align-items: center;
        height: 36px;
        border-bottom: 1px solid #eaecf4;
        cursor: pointer;
        transition: background 0.15s;
        font-size: 0.8rem;
    }

    .gantt-left-row:hover {
        background-color: #eaecf4;
    }

    .gantt-left-row.critical-row {
        background-color: #fff5f5;
    }

    .gantt-left-row.critical-row:hover {
        background-color: #ffe0e0;
    }

    .gantt-left-row .gl-col {
        padding: 0 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .gl-col-num { width: 30px; text-align: center; color: #858796; }
    .gl-col-name { flex: 1; min-width: 0; font-weight: 500; }
    .gl-col-dur { width: 50px; text-align: center; }
    .gl-col-start { width: 80px; text-align: center; font-size: 0.75rem; }
    .gl-col-finish { width: 80px; text-align: center; font-size: 0.75rem; }
    .gl-col-status { width: 80px; text-align: center; }

    /* ===== Right Panel (Timeline) ===== */
    .gantt-right-panel {
        flex: 1;
        overflow-x: auto;
        overflow-y: auto;
        position: relative;
    }

    .gantt-timeline-header {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8f9fc;
        border-bottom: 2px solid #d1d3e2;
        height: 50px;
        display: flex;
        flex-direction: column;
    }

    .gantt-timeline-months {
        display: flex;
        height: 25px;
        align-items: center;
        border-bottom: 1px solid #e3e6f0;
    }

    .gantt-timeline-months .month-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #4e73df;
        text-align: center;
        border-right: 1px solid #d1d3e2;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        white-space: nowrap;
    }

    .gantt-timeline-days {
        display: flex;
        height: 25px;
        align-items: center;
    }

    .gantt-timeline-days .day-label {
        font-size: 0.65rem;
        color: #858796;
        text-align: center;
        border-right: 1px solid #eaecf4;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .gantt-timeline-days .day-label.weekend {
        background-color: #f1f1f4;
        color: #b7b9cc;
    }

    .gantt-timeline-body {
        position: relative;
    }

    .gantt-timeline-row {
        height: 36px;
        border-bottom: 1px solid #eaecf4;
        position: relative;
    }

    .gantt-timeline-row.weekend-col {
        background-color: rgba(241, 241, 244, 0.3);
    }

    /* ===== Gantt Bars ===== */
    .gantt-bar {
        position: absolute;
        height: 22px;
        top: 7px;
        border-radius: 3px;
        display: flex;
        align-items: center;
        padding: 0 6px;
        font-size: 0.7rem;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: pointer;
        z-index: 1;
        transition: box-shadow 0.15s;
        min-width: 4px;
    }

    .gantt-bar:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        z-index: 5;
    }

    .gantt-bar.status-not-started {
        background-color: #858796;
    }

    .gantt-bar.status-in-progress {
        background-color: #4e73df;
    }

    .gantt-bar.status-complete {
        background-color: #1cc88a;
    }

    .gantt-bar.status-blocked {
        background-color: #e74a3b;
    }

    .gantt-bar.critical {
        border: 2px solid #e74a3b;
        box-shadow: 0 0 0 1px rgba(231, 74, 59, 0.3);
    }

    .gantt-bar.milestone {
        width: 18px !important;
        height: 18px !important;
        top: 9px;
        border-radius: 2px;
        transform: rotate(45deg);
        padding: 0;
        min-width: 18px;
    }

    .gantt-bar.milestone .bar-label {
        display: none;
    }

    .gantt-bar .bar-label {
        overflow: hidden;
        text-overflow: ellipsis;
        text-shadow: 0 1px 1px rgba(0,0,0,0.3);
    }

    /* ===== Today Line ===== */
    .gantt-today-line {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e74a3b;
        z-index: 4;
        pointer-events: none;
    }

    .gantt-today-line::before {
        content: 'Today';
        position: absolute;
        top: -18px;
        left: -16px;
        font-size: 0.6rem;
        color: #e74a3b;
        font-weight: 600;
        white-space: nowrap;
    }

    .gantt-today-line::after {
        content: '';
        position: absolute;
        top: 0;
        left: -3px;
        width: 8px;
        height: 8px;
        background: #e74a3b;
        border-radius: 50%;
    }

    /* ===== Dependency Lines (SVG) ===== */
    .gantt-deps-svg {
        position: absolute;
        top: 0;
        left: 0;
        pointer-events: none;
        z-index: 3;
        overflow: visible;
    }

    .gantt-deps-svg .dep-line {
        fill: none;
        stroke: #858796;
        stroke-width: 1.5;
    }

    .gantt-deps-svg .dep-arrow {
        fill: #858796;
    }

    .gantt-deps-svg .dep-line.critical-dep {
        stroke: #e74a3b;
    }

    .gantt-deps-svg .dep-arrow.critical-dep {
        fill: #e74a3b;
    }

    /* ===== Gantt Controls ===== */
    .gantt-controls {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        flex-wrap: wrap;
    }

    .gantt-controls .zoom-controls .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }

    /* ===== Weekend Stripe Overlay ===== */
    .gantt-weekend-stripe {
        position: absolute;
        top: 0;
        bottom: 0;
        background: rgba(241, 241, 244, 0.4);
        pointer-events: none;
        z-index: 0;
    }

    /* ===== Scroll Sync ===== */
    .gantt-left-panel::-webkit-scrollbar,
    .gantt-right-panel::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .gantt-left-panel::-webkit-scrollbar-thumb,
    .gantt-right-panel::-webkit-scrollbar-thumb {
        background: #d1d3e2;
        border-radius: 3px;
    }

    /* ===== Empty State ===== */
    .gantt-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 300px;
        color: #858796;
        font-size: 1rem;
    }

    .gantt-empty i {
        font-size: 2rem;
        margin-right: 10px;
    }

    /* ===== Activity Table Highlights ===== */
    #activityTable .critical-bg {
        background-color: #fff5f5 !important;
    }

    /* ===== Health Grade Card ===== */
    .health-grade-display {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
    }

    /* ===== Resize Handle for Left Panel ===== */
    .gantt-resize-handle {
        width: 4px;
        cursor: col-resize;
        background: transparent;
        position: absolute;
        top: 0;
        bottom: 0;
        right: -2px;
        z-index: 5;
    }

    .gantt-resize-handle:hover {
        background: #4e73df;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- ===== Breadcrumb & Page Header ===== --}}
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.projects.show', $project->proj_id) }}">{{ $project->proj_name }}</a></li>
            <li class="breadcrumb-item active">Schedule</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h5 class="mb-0 font-weight-bold text-primary">
            <i class="fas fa-calendar-alt me-1"></i> Schedule: {{ $project->proj_name }}
        </h5>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-success btn-sm" id="btnCalculate">
                <i class="fas fa-calculator me-1"></i> <span class="btn-text">Calculate Schedule</span>
                <span class="spinner-border spinner-border-sm d-none" role="status" id="calcSpinner"></span>
            </button>
            <button type="button" class="btn btn-primary btn-sm" id="btnAddActivity">
                <i class="fas fa-plus me-1"></i> Add Activity
            </button>
            <a href="{{ route('admin.schedule.lookahead', $project->proj_id) }}" class="btn btn-info btn-sm text-white">
                <i class="fas fa-binoculars me-1"></i> Lookahead
            </a>
            <div class="dropdown">
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog me-1"></i> Settings
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.schedule.calendars', $project->proj_id) }}">
                            <i class="fas fa-clock me-2"></i> Calendars
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('admin.schedule.baselines', $project->proj_id) }}">
                            <i class="fas fa-flag me-2"></i> Baselines
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="#" id="btnExportCsv">
                            <i class="fas fa-file-csv me-2"></i> Export CSV
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" id="btnExportPdf">
                            <i class="fas fa-file-pdf me-2"></i> Export PDF
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ===== Last Run Info ===== --}}
    @if($lastRun)
    <div class="alert alert-light border py-2 px-3 mb-3" style="font-size: 0.85rem;">
        <i class="fas fa-info-circle text-primary me-1"></i>
        Last calculated: <strong>{{ $lastRun->run_created_at->format('M d, Y g:i A') }}</strong>
        &mdash; {{ $lastRun->run_total_activities }} activities, {{ $lastRun->run_critical_count }} critical
        @if($lastRun->run_computation_ms)
            ({{ $lastRun->run_computation_ms }}ms)
        @endif
        @if($lastRun->run_project_finish)
            &mdash; Project finish: <strong>{{ $lastRun->run_project_finish->format('M d, Y') }}</strong>
        @endif
    </div>
    @endif

    {{-- ===== Health Summary Cards ===== --}}
    <div class="row mb-3">
        {{-- Total Activities --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card primary h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Activities</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $healthSummary['total'] ?? 0 }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Critical Path --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card danger h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Critical Path</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                {{ $healthSummary['critical'] ?? 0 }}
                                @if(($healthSummary['near_critical'] ?? 0) > 0)
                                    <small class="text-muted fs-6">(+{{ $healthSummary['near_critical'] }} near)</small>
                                @endif
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Completion % --}}
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card success h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 me-3">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Completion</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($healthSummary['complete_pct'] ?? 0, 1) }}%</div>
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ min($healthSummary['complete_pct'] ?? 0, 100) }}%"></div>
                            </div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                    @if(($healthSummary['overdue'] ?? 0) > 0)
                        <div class="mt-1">
                            <span class="badge bg-danger">{{ $healthSummary['overdue'] }} overdue</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Health Grade --}}
        @php
            $grade = $healthSummary['health_grade'] ?? 'N/A';
            $gradeColors = [
                'A' => 'success', 'A+' => 'success', 'A-' => 'success',
                'B' => 'info', 'B+' => 'info', 'B-' => 'info',
                'C' => 'warning', 'C+' => 'warning', 'C-' => 'warning',
                'D' => 'danger', 'D+' => 'danger', 'D-' => 'danger',
                'F' => 'danger',
            ];
            $gradeColor = $gradeColors[$grade] ?? 'secondary';
        @endphp
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card {{ $gradeColor }} h-100">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-{{ $gradeColor }} text-uppercase mb-1">Health Grade</div>
                            <div class="health-grade-display text-{{ $gradeColor }}">{{ $grade }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Tabs ===== --}}
    <ul class="nav nav-tabs" id="scheduleTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="gantt-tab" data-bs-toggle="tab" data-bs-target="#ganttPane"
                    type="button" role="tab" aria-controls="ganttPane" aria-selected="true">
                <i class="fas fa-chart-gantt me-1"></i> Gantt Chart
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="table-tab" data-bs-toggle="tab" data-bs-target="#tablePane"
                    type="button" role="tab" aria-controls="tablePane" aria-selected="false">
                <i class="fas fa-table me-1"></i> Activity Table
            </button>
        </li>
    </ul>

    <div class="tab-content" id="scheduleTabContent">

        {{-- ============================== --}}
        {{-- TAB 1: GANTT CHART             --}}
        {{-- ============================== --}}
        <div class="tab-pane fade show active" id="ganttPane" role="tabpanel" aria-labelledby="gantt-tab">

            {{-- Controls --}}
            <div class="gantt-controls">
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 text-xs text-muted">From:</label>
                    <input type="date" class="form-control form-control-sm" id="ganttDateFrom" style="width: 140px;">
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 text-xs text-muted">To:</label>
                    <input type="date" class="form-control form-control-sm" id="ganttDateTo" style="width: 140px;">
                </div>
                <div class="zoom-controls btn-group">
                    <button type="button" class="btn btn-outline-secondary" id="btnZoomOut" title="Zoom Out">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnZoomReset" title="Fit to Screen">
                        <i class="fas fa-compress-arrows-alt"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnZoomIn" title="Zoom In">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>
                <div>
                    <select class="form-select form-select-sm" id="ganttFilter" style="width: 150px;">
                        <option value="all">All Activities</option>
                        <option value="critical">Critical Only</option>
                        <option value="in_progress">In Progress</option>
                        <option value="not_started">Not Started</option>
                        <option value="blocked">Blocked</option>
                    </select>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddDependency" title="Add Dependency">
                    <i class="fas fa-link me-1"></i> Add Link
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm ms-auto" id="btnRefreshGantt" title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>

            {{-- Gantt Chart --}}
            <div class="gantt-container" id="ganttContainer" style="height: 500px;">
                <div class="gantt-left-panel" id="ganttLeftPanel">
                    <div class="gantt-left-header">
                        <div class="gl-col gl-col-num">#</div>
                        <div class="gl-col gl-col-name">Activity Name</div>
                        <div class="gl-col gl-col-dur">Days</div>
                        <div class="gl-col gl-col-start">Start</div>
                        <div class="gl-col gl-col-finish">Finish</div>
                        <div class="gl-col gl-col-status">Status</div>
                    </div>
                    <div class="gantt-left-body" id="ganttLeftBody">
                        {{-- Rendered by JS --}}
                    </div>
                </div>
                <div class="gantt-right-panel" id="ganttRightPanel">
                    <div class="gantt-timeline-header" id="ganttTimelineHeader">
                        <div class="gantt-timeline-months" id="ganttMonthsRow"></div>
                        <div class="gantt-timeline-days" id="ganttDaysRow"></div>
                    </div>
                    <div class="gantt-timeline-body" id="ganttTimelineBody">
                        {{-- Bars and lines rendered by JS --}}
                    </div>
                </div>
            </div>

        </div>

        {{-- ============================== --}}
        {{-- TAB 2: ACTIVITY TABLE          --}}
        {{-- ============================== --}}
        <div class="tab-pane fade" id="tablePane" role="tabpanel" aria-labelledby="table-tab">
            <div class="card mt-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0" id="activityTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>Name</th>
                                    <th style="width: 80px;">Type</th>
                                    <th style="width: 70px;">Duration</th>
                                    <th style="width: 100px;">ES</th>
                                    <th style="width: 100px;">EF</th>
                                    <th style="width: 100px;">LS</th>
                                    <th style="width: 100px;">LF</th>
                                    <th style="width: 80px;">Float</th>
                                    <th style="width: 90px;">Status</th>
                                    <th style="width: 60px;">Critical</th>
                                    <th style="width: 130px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $idx => $act)
                                <tr class="{{ $act->act_is_critical ? 'critical-bg' : '' }}">
                                    <td class="text-center text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <a href="#" class="text-decoration-none edit-activity-link"
                                           data-id="{{ $act->act_id }}">
                                            {{ $act->act_name }}
                                        </a>
                                        @if($act->act_description)
                                            <i class="fas fa-info-circle text-muted ms-1" data-bs-toggle="tooltip"
                                               title="{{ $act->act_description }}"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($act->act_type === 'MILESTONE')
                                            <span class="badge bg-dark"><i class="fas fa-diamond me-1"></i>MS</span>
                                        @elseif($act->act_type === 'SUMMARY')
                                            <span class="badge bg-secondary"><i class="fas fa-folder me-1"></i>SUM</span>
                                        @else
                                            <span class="badge bg-primary"><i class="fas fa-check me-1"></i>Task</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $act->act_duration_minutes ? round($act->act_duration_minutes / 510, 1) : 0 }}d
                                    </td>
                                    <td class="text-center" style="font-size: 0.8rem;">
                                        {{ $act->act_early_start ? $act->act_early_start->format('m/d/y') : '--' }}
                                    </td>
                                    <td class="text-center" style="font-size: 0.8rem;">
                                        {{ $act->act_early_finish ? $act->act_early_finish->format('m/d/y') : '--' }}
                                    </td>
                                    <td class="text-center" style="font-size: 0.8rem;">
                                        {{ $act->act_late_start ? $act->act_late_start->format('m/d/y') : '--' }}
                                    </td>
                                    <td class="text-center" style="font-size: 0.8rem;">
                                        {{ $act->act_late_finish ? $act->act_late_finish->format('m/d/y') : '--' }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $floatDays = $act->act_total_float_minutes ? round($act->act_total_float_minutes / 510, 1) : 0;
                                        @endphp
                                        <span class="{{ $floatDays <= 0 ? 'text-danger fw-bold' : ($floatDays <= 5 ? 'text-warning' : 'text-muted') }}">
                                            {{ $floatDays }}d
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $act->status_badge }}">
                                            {{ str_replace('_', ' ', $act->act_status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($act->act_is_critical)
                                            <span class="badge bg-danger"><i class="fas fa-fire"></i> Yes</span>
                                        @else
                                            <span class="text-muted">No</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-edit-activity"
                                                    data-id="{{ $act->act_id }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-record-actuals"
                                                    data-id="{{ $act->act_id }}" data-name="{{ $act->act_name }}" title="Record Actuals">
                                                <i class="fas fa-clipboard-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-delete-activity"
                                                    data-id="{{ $act->act_id }}" data-name="{{ $act->act_name }}" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
</div>

{{-- ============================== --}}
{{-- MODAL: Add/Edit Activity       --}}
{{-- ============================== --}}
<div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="activityModalLabel">
                    <i class="fas fa-plus-circle me-1"></i> Add Activity
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="activityForm">
                <input type="hidden" id="actFormId" name="act_id" value="">
                <input type="hidden" name="act_project_id" value="{{ $project->proj_id }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="actName" class="form-label">Activity Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="actName" name="act_name" required maxlength="255">
                        </div>
                        <div class="col-md-4">
                            <label for="actType" class="form-label">Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="actType" name="act_type" required>
                                <option value="TASK">Task</option>
                                <option value="MILESTONE">Milestone</option>
                                <option value="SUMMARY">Summary</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="actDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="actDescription" name="act_description" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="actDuration" class="form-label">Duration (days) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="actDuration" name="act_duration_days"
                                   min="0" step="0.5" value="1" required>
                            <div class="form-text">8.5 hours per work day</div>
                        </div>
                        <div class="col-md-4">
                            <label for="actCalendar" class="form-label">Calendar</label>
                            <select class="form-select" id="actCalendar" name="act_calendar_id">
                                <option value="">-- Project Default --</option>
                                @foreach($calendars as $cal)
                                    <option value="{{ $cal->cal_id }}">
                                        {{ $cal->cal_name }}{{ $cal->cal_is_default ? ' (Default)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="actPriority" class="form-label">Priority</label>
                            <input type="number" class="form-control" id="actPriority" name="act_priority"
                                   min="0" max="9999" value="500">
                        </div>
                        <div class="col-md-6">
                            <label for="actWbs" class="form-label">WBS Node</label>
                            <select class="form-select" id="actWbs" name="act_wbs_id">
                                <option value="">-- None --</option>
                                {{-- Populated via JS or inline from controller --}}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="actColor" class="form-label">Color</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="actColorPicker" value="#4e73df">
                                <input type="text" class="form-control" id="actColor" name="act_color" placeholder="#4e73df" maxlength="7">
                                <button type="button" class="btn btn-outline-secondary" id="btnClearColor" title="Clear color">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="actConstraintType" class="form-label">Constraint Type</label>
                            <select class="form-select" id="actConstraintType" name="act_constraint_type">
                                <option value="NONE">None</option>
                                <option value="SNET">Start No Earlier Than</option>
                                <option value="FNLT">Finish No Later Than</option>
                                <option value="MSO">Must Start On</option>
                                <option value="MFO">Must Finish On</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="constraintDateGroup" style="display: none;">
                            <label for="actConstraintDate" class="form-label">Constraint Date</label>
                            <input type="datetime-local" class="form-control" id="actConstraintDate" name="act_constraint_date">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveActivity">
                        <i class="fas fa-save me-1"></i> Save Activity
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================== --}}
{{-- MODAL: Record Actuals          --}}
{{-- ============================== --}}
<div class="modal fade" id="actualsModal" tabindex="-1" aria-labelledby="actualsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="actualsModalLabel">
                    <i class="fas fa-clipboard-check me-1"></i> Record Actuals
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="actualsForm">
                <input type="hidden" id="actualsActId" name="act_id" value="">
                <div class="modal-body">
                    <div class="alert alert-info py-2" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle me-1"></i>
                        Recording actuals for: <strong id="actualsActName"></strong>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="actualStart" class="form-label">Actual Start</label>
                            <input type="datetime-local" class="form-control" id="actualStart" name="aca_actual_start">
                        </div>
                        <div class="col-md-6">
                            <label for="actualFinish" class="form-label">Actual Finish</label>
                            <input type="datetime-local" class="form-control" id="actualFinish" name="aca_actual_finish">
                        </div>
                        <div class="col-md-6" id="remainingDurationGroup">
                            <label for="actualRemaining" class="form-label">Remaining Duration (days)</label>
                            <input type="number" class="form-control" id="actualRemaining" name="aca_remaining_duration_days"
                                   min="0" step="0.5">
                            <div class="form-text">Only when started but not finished</div>
                        </div>
                        <div class="col-md-12">
                            <label for="actualNotes" class="form-label">Notes</label>
                            <textarea class="form-control" id="actualNotes" name="aca_note" rows="3"
                                      placeholder="Progress notes, issues, etc."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="btnSaveActuals">
                        <i class="fas fa-save me-1"></i> Save Actuals
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================== --}}
{{-- MODAL: Add Dependency           --}}
{{-- ============================== --}}
<div class="modal fade" id="dependencyModal" tabindex="-1" aria-labelledby="dependencyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="dependencyModalLabel">
                    <i class="fas fa-link me-1"></i> Add Dependency
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="dependencyForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="depPredecessor" class="form-label">Predecessor <span class="text-danger">*</span></label>
                            <select class="form-select" id="depPredecessor" name="dep_predecessor_id" required>
                                <option value="">-- Select Predecessor --</option>
                                @foreach($activities as $act)
                                    <option value="{{ $act->act_id }}">{{ $act->act_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="depSuccessor" class="form-label">Successor <span class="text-danger">*</span></label>
                            <select class="form-select" id="depSuccessor" name="dep_successor_id" required>
                                <option value="">-- Select Successor --</option>
                                @foreach($activities as $act)
                                    <option value="{{ $act->act_id }}">{{ $act->act_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="depType" class="form-label">Type</label>
                            <select class="form-select" id="depType" name="dep_type">
                                <option value="FS">Finish-to-Start (FS)</option>
                                <option value="SS">Start-to-Start (SS)</option>
                                <option value="FF">Finish-to-Finish (FF)</option>
                                <option value="SF">Start-to-Finish (SF)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="depLag" class="form-label">Lag (days)</label>
                            <input type="number" class="form-control" id="depLag" name="dep_lag_days"
                                   step="0.5" value="0">
                            <div class="form-text">Negative = lead time</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white" id="btnSaveDependency">
                        <i class="fas fa-save me-1"></i> Save Dependency
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================== --}}
{{-- MODAL: Delete Confirmation      --}}
{{-- ============================== --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash me-1"></i> Delete Activity</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteActName"></strong>?</p>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">This will also remove all dependencies linked to this activity.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="btnConfirmDelete">
                    <i class="fas fa-trash me-1"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {

    // ========================================================
    // CONSTANTS & CONFIG
    // ========================================================
    const MINUTES_PER_DAY = 510; // 8.5 hours standard work day
    const ROW_HEIGHT = 36;
    const BAR_HEIGHT = 22;
    const BAR_TOP = 7;
    const MIN_PX_PER_DAY = 8;
    const MAX_PX_PER_DAY = 120;
    const DEFAULT_PX_PER_DAY = 30;

    const projectId = {{ $project->proj_id }};
    let pxPerDay = DEFAULT_PX_PER_DAY;
    let ganttData = null;
    let filteredActivities = [];
    let dateFrom = null;
    let dateTo = null;

    // ========================================================
    // ROUTE URLS (avoid Blade syntax issues in JS)
    // ========================================================
    const URLS = {
        calculate:    '{{ route("admin.schedule.calculate", $project->proj_id) }}',
        ganttData:    '{{ route("admin.schedule.gantt-data", $project->proj_id) }}',
        storeActivity: '{{ route("admin.schedule.activities.store", $project->proj_id) }}',
        updateActivity: '{{ route("admin.schedule.activities.update", [$project->proj_id, ":id"]) }}',
        deleteActivity: '{{ route("admin.schedule.activities.destroy", [$project->proj_id, ":id"]) }}',
        showActivity:  '{{ route("admin.schedule.activities.show", [$project->proj_id, ":id"]) }}',
        storeActuals:  '{{ route("admin.schedule.activities.actuals", [$project->proj_id, ":id"]) }}',
        storeDependency: '{{ route("admin.schedule.dependencies.store", $project->proj_id) }}',
        exportCsv:    '{{ route("admin.schedule.export.csv", $project->proj_id) }}',
        exportPdf:    '{{ route("admin.schedule.export.pdf", $project->proj_id) }}',
    };

    function buildUrl(template, id) {
        return template.replace(':id', id);
    }

    // ========================================================
    // INIT: Parse gantt data and render
    // ========================================================
    try {
        ganttData = JSON.parse('{!! addslashes($ganttDataJson) !!}');
    } catch(e) {
        ganttData = { activities: [], dependencies: [] };
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });

    // Initialize activity table DataTable (skip auto-init)
    var activityDT = $('#activityTable').DataTable({
        responsive: true,
        pageLength: 50,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [11] }
        ],
        language: {
            emptyTable: "No activities found. Click 'Add Activity' to create one."
        }
    });

    // Render the Gantt on page load
    renderGantt(ganttData);

    // ========================================================
    // GANTT CHART: Core Rendering
    // ========================================================

    function parseDate(str) {
        if (!str) return null;
        var d = new Date(str);
        return isNaN(d.getTime()) ? null : d;
    }

    function formatDateShort(d) {
        if (!d) return '--';
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        var dd = String(d.getDate()).padStart(2, '0');
        var yy = String(d.getFullYear()).slice(-2);
        return mm + '/' + dd + '/' + yy;
    }

    function formatDateISO(d) {
        if (!d) return '';
        return d.getFullYear() + '-' +
               String(d.getMonth() + 1).padStart(2, '0') + '-' +
               String(d.getDate()).padStart(2, '0');
    }

    function daysBetween(d1, d2) {
        return (d2 - d1) / (1000 * 60 * 60 * 24);
    }

    function addDays(d, n) {
        var r = new Date(d);
        r.setDate(r.getDate() + n);
        return r;
    }

    function isWeekend(d) {
        var day = d.getDay();
        return day === 0 || day === 6;
    }

    function getMonthName(d) {
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return months[d.getMonth()];
    }

    function calculateScale(activities) {
        var minDate = null;
        var maxDate = null;

        activities.forEach(function(act) {
            var es = parseDate(act.early_start);
            var ef = parseDate(act.early_finish);
            var ls = parseDate(act.late_start);
            var lf = parseDate(act.late_finish);

            [es, ef, ls, lf].forEach(function(d) {
                if (d) {
                    if (!minDate || d < minDate) minDate = new Date(d);
                    if (!maxDate || d > maxDate) maxDate = new Date(d);
                }
            });
        });

        if (!minDate || !maxDate) {
            minDate = new Date();
            maxDate = addDays(new Date(), 30);
        }

        // Apply user date range if set
        if (dateFrom) {
            var df = new Date(dateFrom);
            if (!isNaN(df.getTime())) minDate = df;
        }
        if (dateTo) {
            var dt = new Date(dateTo);
            if (!isNaN(dt.getTime())) maxDate = dt;
        }

        // Add padding: 7 days before, 14 days after
        minDate = addDays(minDate, -7);
        maxDate = addDays(maxDate, 14);

        // Snap to first of month for min
        minDate.setDate(1);

        var totalDays = Math.ceil(daysBetween(minDate, maxDate));
        if (totalDays < 14) totalDays = 14;

        return {
            startDate: minDate,
            endDate: maxDate,
            totalDays: totalDays,
            totalWidth: totalDays * pxPerDay
        };
    }

    function getStatusClass(status) {
        switch(status) {
            case 'IN_PROGRESS': return 'status-in-progress';
            case 'COMPLETE':    return 'status-complete';
            case 'BLOCKED':     return 'status-blocked';
            default:            return 'status-not-started';
        }
    }

    function getStatusLabel(status) {
        switch(status) {
            case 'NOT_STARTED': return 'Not Started';
            case 'IN_PROGRESS': return 'In Progress';
            case 'COMPLETE':    return 'Complete';
            case 'BLOCKED':     return 'Blocked';
            default:            return status || 'N/A';
        }
    }

    function getStatusBadge(status) {
        var colors = {
            'NOT_STARTED': 'secondary',
            'IN_PROGRESS': 'info',
            'COMPLETE':    'success',
            'BLOCKED':     'danger'
        };
        var label = getStatusLabel(status);
        var color = colors[status] || 'secondary';
        return '<span class="badge bg-' + color + '" style="font-size:0.65rem;">' + label + '</span>';
    }

    function applyFilter(activities) {
        var filter = $('#ganttFilter').val();
        if (filter === 'all') return activities;
        if (filter === 'critical') return activities.filter(function(a) { return a.is_critical; });
        if (filter === 'in_progress') return activities.filter(function(a) { return a.status === 'IN_PROGRESS'; });
        if (filter === 'not_started') return activities.filter(function(a) { return a.status === 'NOT_STARTED'; });
        if (filter === 'blocked') return activities.filter(function(a) { return a.status === 'BLOCKED'; });
        return activities;
    }

    function renderGantt(data) {
        if (!data || !data.activities || data.activities.length === 0) {
            $('#ganttLeftBody').html('');
            $('#ganttMonthsRow').html('');
            $('#ganttDaysRow').html('');
            $('#ganttTimelineBody').html(
                '<div class="gantt-empty">' +
                '<i class="fas fa-calendar-plus"></i> No activities to display. Add an activity to get started.' +
                '</div>'
            );
            return;
        }

        filteredActivities = applyFilter(data.activities);
        if (filteredActivities.length === 0) {
            $('#ganttLeftBody').html('');
            $('#ganttTimelineBody').html(
                '<div class="gantt-empty">' +
                '<i class="fas fa-filter"></i> No activities match the current filter.' +
                '</div>'
            );
            return;
        }

        var scale = calculateScale(filteredActivities);

        // Set date input defaults
        if (!dateFrom) $('#ganttDateFrom').val(formatDateISO(scale.startDate));
        if (!dateTo) $('#ganttDateTo').val(formatDateISO(scale.endDate));

        renderLeftPanel(filteredActivities);
        renderTimelineHeader(scale);
        renderTimelineBars(filteredActivities, data.dependencies || [], scale);
    }

    // ===== LEFT PANEL =====
    function renderLeftPanel(activities) {
        var html = '';
        activities.forEach(function(act, idx) {
            var durDays = act.duration_minutes ? (act.duration_minutes / MINUTES_PER_DAY).toFixed(1) : '0';
            var es = parseDate(act.early_start);
            var ef = parseDate(act.early_finish);
            var critClass = act.is_critical ? ' critical-row' : '';

            html += '<div class="gantt-left-row' + critClass + '" data-act-id="' + act.id + '">' +
                '<div class="gl-col gl-col-num">' + (idx + 1) + '</div>' +
                '<div class="gl-col gl-col-name" title="' + escapeHtml(act.name) + '">' +
                    (act.type === 'MILESTONE' ? '<i class="fas fa-diamond text-dark me-1" style="font-size:0.6rem;"></i>' : '') +
                    escapeHtml(act.name) +
                '</div>' +
                '<div class="gl-col gl-col-dur">' + durDays + '</div>' +
                '<div class="gl-col gl-col-start">' + formatDateShort(es) + '</div>' +
                '<div class="gl-col gl-col-finish">' + formatDateShort(ef) + '</div>' +
                '<div class="gl-col gl-col-status">' + getStatusBadge(act.status) + '</div>' +
                '</div>';
        });
        $('#ganttLeftBody').html(html);
    }

    // ===== TIMELINE HEADER =====
    function renderTimelineHeader(scale) {
        var monthsHtml = '';
        var daysHtml = '';
        var currentDate = new Date(scale.startDate);
        var currentMonth = -1;
        var monthStartIdx = 0;
        var monthDays = [];

        for (var i = 0; i < scale.totalDays; i++) {
            var d = addDays(scale.startDate, i);
            var m = d.getMonth();
            var y = d.getFullYear();
            var weekendClass = isWeekend(d) ? ' weekend' : '';

            // Track month boundaries
            if (m !== currentMonth) {
                if (currentMonth !== -1) {
                    monthDays.push({ label: getMonthName(addDays(scale.startDate, monthStartIdx)) + ' ' + addDays(scale.startDate, monthStartIdx).getFullYear(), days: i - monthStartIdx });
                }
                currentMonth = m;
                monthStartIdx = i;
            }

            daysHtml += '<div class="day-label' + weekendClass + '" style="width:' + pxPerDay + 'px;min-width:' + pxPerDay + 'px;">';
            if (pxPerDay >= 20) {
                daysHtml += d.getDate();
            } else if (pxPerDay >= 14 && d.getDate() % 5 === 0) {
                daysHtml += d.getDate();
            }
            daysHtml += '</div>';
        }

        // Last month
        monthDays.push({
            label: getMonthName(addDays(scale.startDate, monthStartIdx)) + ' ' + addDays(scale.startDate, monthStartIdx).getFullYear(),
            days: scale.totalDays - monthStartIdx
        });

        monthDays.forEach(function(md) {
            var w = md.days * pxPerDay;
            monthsHtml += '<div class="month-label" style="width:' + w + 'px;min-width:' + w + 'px;">' + md.label + '</div>';
        });

        $('#ganttMonthsRow').html(monthsHtml);
        $('#ganttDaysRow').html(daysHtml);
    }

    // ===== TIMELINE BARS & DEPENDENCIES =====
    function renderTimelineBars(activities, dependencies, scale) {
        var bodyHtml = '';
        var totalHeight = activities.length * ROW_HEIGHT;

        // Build activity index for dependency drawing
        var actIndex = {};
        activities.forEach(function(act, idx) {
            actIndex[act.id] = idx;
        });

        // Weekend stripes
        var stripes = '';
        for (var d = 0; d < scale.totalDays; d++) {
            var dt = addDays(scale.startDate, d);
            if (isWeekend(dt)) {
                stripes += '<div class="gantt-weekend-stripe" style="left:' + (d * pxPerDay) + 'px;width:' + pxPerDay + 'px;height:' + totalHeight + 'px;"></div>';
            }
        }

        // Activity rows and bars
        var bars = '';
        activities.forEach(function(act, idx) {
            bodyHtml += '<div class="gantt-timeline-row" data-act-id="' + act.id + '"></div>';
            bars += drawBar(act, idx, scale);
        });

        // Today line
        var today = new Date();
        today.setHours(0,0,0,0);
        var todayOffset = daysBetween(scale.startDate, today);
        var todayLine = '';
        if (todayOffset >= 0 && todayOffset <= scale.totalDays) {
            todayLine = '<div class="gantt-today-line" style="left:' + (todayOffset * pxPerDay) + 'px;height:' + totalHeight + 'px;"></div>';
        }

        // Dependency SVG
        var depsSvg = drawDependencies(dependencies, activities, actIndex, scale, totalHeight);

        var bodyContent = '<div style="position:relative;width:' + scale.totalWidth + 'px;height:' + totalHeight + 'px;">' +
            stripes + bodyHtml + bars + todayLine + depsSvg +
            '</div>';

        $('#ganttTimelineBody').html(bodyContent);
    }

    function drawBar(act, rowIdx, scale) {
        var es = parseDate(act.early_start);
        var ef = parseDate(act.early_finish);
        if (!es || !ef) return '';

        var startOffset = daysBetween(scale.startDate, es);
        var endOffset = daysBetween(scale.startDate, ef);
        var barLeft = startOffset * pxPerDay;
        var barWidth = Math.max((endOffset - startOffset) * pxPerDay, 4);
        var top = (rowIdx * ROW_HEIGHT) + BAR_TOP;

        var statusClass = getStatusClass(act.status);
        var critClass = act.is_critical ? ' critical' : '';
        var milestoneClass = act.type === 'MILESTONE' ? ' milestone' : '';

        var customStyle = '';
        if (act.color && act.type !== 'MILESTONE') {
            customStyle = 'background-color:' + act.color + ';';
        }

        // For milestones, position the diamond at the start date
        if (act.type === 'MILESTONE') {
            return '<div class="gantt-bar ' + statusClass + critClass + milestoneClass + '" ' +
                'style="left:' + (barLeft - 9) + 'px;top:' + (rowIdx * ROW_HEIGHT + 9) + 'px;' + customStyle + '" ' +
                'data-act-id="' + act.id + '" title="' + escapeHtml(act.name) + ' (Milestone)">' +
                '<span class="bar-label"></span>' +
                '</div>';
        }

        var label = pxPerDay >= 16 && barWidth > 40 ? escapeHtml(act.name) : '';

        return '<div class="gantt-bar ' + statusClass + critClass + '" ' +
            'style="left:' + barLeft + 'px;width:' + barWidth + 'px;top:' + top + 'px;' + customStyle + '" ' +
            'data-act-id="' + act.id + '" title="' + escapeHtml(act.name) + '">' +
            '<span class="bar-label">' + label + '</span>' +
            '</div>';
    }

    function drawDependencies(dependencies, activities, actIndex, scale, totalHeight) {
        if (!dependencies || dependencies.length === 0) return '';

        var lines = '';
        dependencies.forEach(function(dep) {
            var predIdx = actIndex[dep.predecessor_id];
            var succIdx = actIndex[dep.successor_id];
            if (predIdx === undefined || succIdx === undefined) return;

            var predAct = activities[predIdx];
            var succAct = activities[succIdx];
            if (!predAct || !succAct) return;

            var predEs = parseDate(predAct.early_start);
            var predEf = parseDate(predAct.early_finish);
            var succEs = parseDate(succAct.early_start);
            var succEf = parseDate(succAct.early_finish);

            if (!predEs || !predEf || !succEs || !succEf) return;

            // Determine start/end points based on dep type
            var x1, y1, x2, y2;
            var type = dep.type || 'FS';

            var predStartPx = daysBetween(scale.startDate, predEs) * pxPerDay;
            var predEndPx = daysBetween(scale.startDate, predEf) * pxPerDay;
            var succStartPx = daysBetween(scale.startDate, succEs) * pxPerDay;
            var succEndPx = daysBetween(scale.startDate, succEf) * pxPerDay;

            var predMidY = (predIdx * ROW_HEIGHT) + (ROW_HEIGHT / 2);
            var succMidY = (succIdx * ROW_HEIGHT) + (ROW_HEIGHT / 2);

            switch(type) {
                case 'FS':
                    x1 = predEndPx; y1 = predMidY;
                    x2 = succStartPx; y2 = succMidY;
                    break;
                case 'SS':
                    x1 = predStartPx; y1 = predMidY;
                    x2 = succStartPx; y2 = succMidY;
                    break;
                case 'FF':
                    x1 = predEndPx; y1 = predMidY;
                    x2 = succEndPx; y2 = succMidY;
                    break;
                case 'SF':
                    x1 = predStartPx; y1 = predMidY;
                    x2 = succEndPx; y2 = succMidY;
                    break;
                default:
                    x1 = predEndPx; y1 = predMidY;
                    x2 = succStartPx; y2 = succMidY;
            }

            var critClass = (predAct.is_critical && succAct.is_critical) ? ' critical-dep' : '';

            // Draw path: go right from pred, then down/up, then right to succ
            var midX = x1 + 10;
            if (midX > x2 - 10) midX = (x1 + x2) / 2;

            var path = 'M' + x1 + ',' + y1 +
                       ' H' + midX +
                       ' V' + y2 +
                       ' H' + x2;

            lines += '<path class="dep-line' + critClass + '" d="' + path + '"/>';

            // Arrow head at the end point
            var arrowSize = 5;
            lines += '<polygon class="dep-arrow' + critClass + '" points="' +
                x2 + ',' + y2 + ' ' +
                (x2 - arrowSize) + ',' + (y2 - arrowSize) + ' ' +
                (x2 - arrowSize) + ',' + (y2 + arrowSize) + '"/>';
        });

        return '<svg class="gantt-deps-svg" width="' + (scale.totalWidth) + '" height="' + totalHeight + '">' +
            lines + '</svg>';
    }

    // ========================================================
    // SCROLL SYNC between left and right panels
    // ========================================================
    var leftPanel = document.getElementById('ganttLeftPanel');
    var rightPanel = document.getElementById('ganttRightPanel');
    var syncing = false;

    if (leftPanel && rightPanel) {
        leftPanel.addEventListener('scroll', function() {
            if (syncing) return;
            syncing = true;
            rightPanel.scrollTop = leftPanel.scrollTop;
            syncing = false;
        });

        rightPanel.addEventListener('scroll', function() {
            if (syncing) return;
            syncing = true;
            leftPanel.scrollTop = rightPanel.scrollTop;
            syncing = false;
        });
    }

    // ========================================================
    // ZOOM CONTROLS
    // ========================================================
    $('#btnZoomIn').on('click', function() {
        pxPerDay = Math.min(pxPerDay + 6, MAX_PX_PER_DAY);
        renderGantt(ganttData);
    });

    $('#btnZoomOut').on('click', function() {
        pxPerDay = Math.max(pxPerDay - 6, MIN_PX_PER_DAY);
        renderGantt(ganttData);
    });

    $('#btnZoomReset').on('click', function() {
        // Fit to visible width
        if (ganttData && ganttData.activities && ganttData.activities.length > 0) {
            var acts = applyFilter(ganttData.activities);
            var sc = calculateScale(acts);
            var panelWidth = $('#ganttRightPanel').width();
            if (panelWidth > 0 && sc.totalDays > 0) {
                pxPerDay = Math.max(MIN_PX_PER_DAY, Math.min(MAX_PX_PER_DAY, Math.floor(panelWidth / sc.totalDays)));
            }
        } else {
            pxPerDay = DEFAULT_PX_PER_DAY;
        }
        renderGantt(ganttData);
    });

    // ========================================================
    // DATE RANGE & FILTER
    // ========================================================
    $('#ganttDateFrom').on('change', function() {
        dateFrom = $(this).val() || null;
        renderGantt(ganttData);
    });

    $('#ganttDateTo').on('change', function() {
        dateTo = $(this).val() || null;
        renderGantt(ganttData);
    });

    $('#ganttFilter').on('change', function() {
        renderGantt(ganttData);
    });

    // ========================================================
    // REFRESH: Fetch new data from server and re-render
    // ========================================================
    function refreshSchedule() {
        $.ajax({
            url: URLS.ganttData,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                ganttData = response;
                renderGantt(ganttData);
                // Also refresh the page to update the table and health cards
                // (or update inline if desired)
            },
            error: function(xhr) {
                showToast('Failed to refresh schedule data.', 'danger');
            }
        });
    }

    $('#btnRefreshGantt').on('click', function() {
        refreshSchedule();
    });

    // ========================================================
    // CALCULATE SCHEDULE
    // ========================================================
    $('#btnCalculate').on('click', function() {
        var $btn = $(this);
        var $text = $btn.find('.btn-text');
        var $spinner = $btn.find('#calcSpinner');

        $btn.prop('disabled', true);
        $text.text('Calculating...');
        $spinner.removeClass('d-none');

        $.ajax({
            url: URLS.calculate,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                showToast('Schedule calculated successfully!', 'success');
                // Reload the page to show updated data
                window.location.reload();
            },
            error: function(xhr) {
                var msg = 'Calculation failed.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showToast(msg, 'danger');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $text.text('Calculate Schedule');
                $spinner.addClass('d-none');
            }
        });
    });

    // ========================================================
    // ACTIVITY MODAL: Add / Edit
    // ========================================================
    var activityModal = new bootstrap.Modal(document.getElementById('activityModal'));

    // Constraint type toggle
    $('#actConstraintType').on('change', function() {
        if ($(this).val() === 'NONE') {
            $('#constraintDateGroup').hide();
            $('#actConstraintDate').val('');
        } else {
            $('#constraintDateGroup').show();
        }
    });

    // Color picker sync
    $('#actColorPicker').on('input', function() {
        $('#actColor').val($(this).val());
    });
    $('#actColor').on('input', function() {
        var v = $(this).val();
        if (/^#[0-9a-fA-F]{6}$/.test(v)) {
            $('#actColorPicker').val(v);
        }
    });
    $('#btnClearColor').on('click', function() {
        $('#actColor').val('');
        $('#actColorPicker').val('#4e73df');
    });

    // Duration field: hide for milestones
    $('#actType').on('change', function() {
        if ($(this).val() === 'MILESTONE') {
            $('#actDuration').val(0).prop('readonly', true);
        } else {
            $('#actDuration').prop('readonly', false);
        }
    });

    // Open Add Activity Modal
    $('#btnAddActivity').on('click', function() {
        openActivityModal(null);
    });

    function openActivityModal(activity) {
        // Reset form
        $('#activityForm')[0].reset();
        $('#actFormId').val('');
        $('#constraintDateGroup').hide();
        $('#actColorPicker').val('#4e73df');
        $('#actDuration').prop('readonly', false);

        if (activity) {
            // Edit mode
            $('#activityModalLabel').html('<i class="fas fa-edit me-1"></i> Edit Activity');
            $('#actFormId').val(activity.act_id || activity.id);
            $('#actName').val(activity.act_name || activity.name);
            $('#actDescription').val(activity.act_description || activity.description || '');
            $('#actType').val(activity.act_type || activity.type || 'TASK');

            var durMinutes = activity.act_duration_minutes || activity.duration_minutes || 0;
            $('#actDuration').val((durMinutes / MINUTES_PER_DAY).toFixed(1));

            $('#actCalendar').val(activity.act_calendar_id || activity.calendar_id || '');
            $('#actPriority').val(activity.act_priority || activity.priority || 500);
            $('#actWbs').val(activity.act_wbs_id || activity.wbs_id || '');

            var color = activity.act_color || activity.color || '';
            $('#actColor').val(color);
            if (color) $('#actColorPicker').val(color);

            var ct = activity.act_constraint_type || activity.constraint_type || 'NONE';
            $('#actConstraintType').val(ct);
            if (ct !== 'NONE') {
                $('#constraintDateGroup').show();
                var cd = activity.act_constraint_date || activity.constraint_date || '';
                if (cd) {
                    // Convert to datetime-local format
                    var cdate = new Date(cd);
                    if (!isNaN(cdate.getTime())) {
                        $('#actConstraintDate').val(cdate.toISOString().slice(0, 16));
                    }
                }
            }

            if ((activity.act_type || activity.type) === 'MILESTONE') {
                $('#actDuration').val(0).prop('readonly', true);
            }
        } else {
            // Add mode
            $('#activityModalLabel').html('<i class="fas fa-plus-circle me-1"></i> Add Activity');
        }

        activityModal.show();
    }

    // Save activity form
    $('#activityForm').on('submit', function(e) {
        e.preventDefault();

        var actId = $('#actFormId').val();
        var url = actId ? buildUrl(URLS.updateActivity, actId) : URLS.storeActivity;
        var method = actId ? 'PUT' : 'POST';

        // Convert duration from days to minutes
        var durationDays = parseFloat($('#actDuration').val()) || 0;
        var durationMinutes = Math.round(durationDays * MINUTES_PER_DAY);

        var formData = {
            act_name: $('#actName').val(),
            act_description: $('#actDescription').val(),
            act_type: $('#actType').val(),
            act_duration_minutes: durationMinutes,
            act_calendar_id: $('#actCalendar').val() || null,
            act_priority: parseInt($('#actPriority').val()) || 500,
            act_wbs_id: $('#actWbs').val() || null,
            act_color: $('#actColor').val() || null,
            act_constraint_type: $('#actConstraintType').val(),
            act_constraint_date: $('#actConstraintDate').val() || null,
            act_project_id: projectId
        };

        var $btn = $('#btnSaveActivity');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        $.ajax({
            url: url,
            type: method,
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                activityModal.hide();
                showToast(actId ? 'Activity updated successfully.' : 'Activity created successfully.', 'success');
                // Reload to refresh everything
                window.location.reload();
            },
            error: function(xhr) {
                var msg = 'Failed to save activity.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                }
                showToast(msg, 'danger');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Activity');
            }
        });
    });

    // ========================================================
    // EDIT ACTIVITY: click handlers
    // ========================================================

    // From Gantt bar or left panel row
    $(document).on('click', '.gantt-bar, .gantt-left-row', function() {
        var actId = $(this).data('act-id');
        if (!actId) return;
        loadAndEditActivity(actId);
    });

    // From table edit button
    $(document).on('click', '.btn-edit-activity, .edit-activity-link', function(e) {
        e.preventDefault();
        var actId = $(this).data('id');
        if (!actId) return;
        loadAndEditActivity(actId);
    });

    function loadAndEditActivity(actId) {
        $.ajax({
            url: buildUrl(URLS.showActivity, actId),
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                openActivityModal(response);
            },
            error: function() {
                showToast('Failed to load activity details.', 'danger');
            }
        });
    }

    // ========================================================
    // DELETE ACTIVITY
    // ========================================================
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    var deleteActId = null;

    $(document).on('click', '.btn-delete-activity', function() {
        deleteActId = $(this).data('id');
        $('#deleteActName').text($(this).data('name'));
        deleteModal.show();
    });

    $('#btnConfirmDelete').on('click', function() {
        if (!deleteActId) return;

        var $btn = $(this);
        $btn.prop('disabled', true);

        $.ajax({
            url: buildUrl(URLS.deleteActivity, deleteActId),
            type: 'DELETE',
            dataType: 'json',
            success: function() {
                deleteModal.hide();
                showToast('Activity deleted.', 'success');
                window.location.reload();
            },
            error: function(xhr) {
                var msg = 'Failed to delete activity.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showToast(msg, 'danger');
            },
            complete: function() {
                $btn.prop('disabled', false);
                deleteActId = null;
            }
        });
    });

    // ========================================================
    // RECORD ACTUALS MODAL
    // ========================================================
    var actualsModal = new bootstrap.Modal(document.getElementById('actualsModal'));

    $(document).on('click', '.btn-record-actuals', function() {
        var actId = $(this).data('id');
        var actName = $(this).data('name');
        openActualsModal(actId, actName);
    });

    function openActualsModal(actId, actName) {
        $('#actualsForm')[0].reset();
        $('#actualsActId').val(actId);
        $('#actualsActName').text(actName || 'Activity #' + actId);
        $('#remainingDurationGroup').show();

        // Try to load existing actuals
        $.ajax({
            url: buildUrl(URLS.showActivity, actId),
            type: 'GET',
            dataType: 'json',
            success: function(act) {
                if (act.actuals) {
                    if (act.actuals.aca_actual_start) {
                        var as = new Date(act.actuals.aca_actual_start);
                        if (!isNaN(as.getTime())) {
                            $('#actualStart').val(as.toISOString().slice(0, 16));
                        }
                    }
                    if (act.actuals.aca_actual_finish) {
                        var af = new Date(act.actuals.aca_actual_finish);
                        if (!isNaN(af.getTime())) {
                            $('#actualFinish').val(af.toISOString().slice(0, 16));
                        }
                    }
                    if (act.actuals.aca_remaining_duration_minutes) {
                        $('#actualRemaining').val((act.actuals.aca_remaining_duration_minutes / MINUTES_PER_DAY).toFixed(1));
                    }
                    if (act.actuals.aca_note) {
                        $('#actualNotes').val(act.actuals.aca_note);
                    }
                }
                updateRemainingVisibility();
            }
        });

        actualsModal.show();
    }

    // Show remaining only when start is set but finish is not
    function updateRemainingVisibility() {
        var hasStart = $('#actualStart').val() !== '';
        var hasFinish = $('#actualFinish').val() !== '';
        if (hasStart && !hasFinish) {
            $('#remainingDurationGroup').show();
        } else {
            $('#remainingDurationGroup').hide();
        }
    }

    $('#actualStart, #actualFinish').on('change', updateRemainingVisibility);

    // Save actuals
    $('#actualsForm').on('submit', function(e) {
        e.preventDefault();

        var actId = $('#actualsActId').val();
        var remainDays = parseFloat($('#actualRemaining').val()) || 0;

        var formData = {
            aca_actual_start: $('#actualStart').val() || null,
            aca_actual_finish: $('#actualFinish').val() || null,
            aca_remaining_duration_minutes: Math.round(remainDays * MINUTES_PER_DAY),
            aca_note: $('#actualNotes').val() || null
        };

        var $btn = $('#btnSaveActuals');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        $.ajax({
            url: buildUrl(URLS.storeActuals, actId),
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function() {
                actualsModal.hide();
                showToast('Actuals recorded successfully.', 'success');
                window.location.reload();
            },
            error: function(xhr) {
                var msg = 'Failed to save actuals.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showToast(msg, 'danger');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Actuals');
            }
        });
    });

    // ========================================================
    // DEPENDENCY MODAL
    // ========================================================
    var dependencyModal = new bootstrap.Modal(document.getElementById('dependencyModal'));

    $('#btnAddDependency').on('click', function() {
        $('#dependencyForm')[0].reset();
        dependencyModal.show();
    });

    $('#dependencyForm').on('submit', function(e) {
        e.preventDefault();

        var predId = $('#depPredecessor').val();
        var succId = $('#depSuccessor').val();

        if (!predId || !succId) {
            showToast('Please select both predecessor and successor.', 'warning');
            return;
        }
        if (predId === succId) {
            showToast('Predecessor and successor cannot be the same activity.', 'warning');
            return;
        }

        var lagDays = parseFloat($('#depLag').val()) || 0;

        var formData = {
            dep_project_id: projectId,
            dep_predecessor_id: parseInt(predId),
            dep_successor_id: parseInt(succId),
            dep_type: $('#depType').val(),
            dep_lag_minutes: Math.round(lagDays * MINUTES_PER_DAY)
        };

        var $btn = $('#btnSaveDependency');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        $.ajax({
            url: URLS.storeDependency,
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            success: function() {
                dependencyModal.hide();
                showToast('Dependency created successfully.', 'success');
                window.location.reload();
            },
            error: function(xhr) {
                var msg = 'Failed to create dependency.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showToast(msg, 'danger');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Dependency');
            }
        });
    });

    // ========================================================
    // EXPORT
    // ========================================================
    $('#btnExportCsv').on('click', function(e) {
        e.preventDefault();
        window.location.href = URLS.exportCsv;
    });

    $('#btnExportPdf').on('click', function(e) {
        e.preventDefault();
        window.location.href = URLS.exportPdf;
    });

    // ========================================================
    // UTILITY: Toast notification
    // ========================================================
    function showToast(message, type) {
        type = type || 'info';
        var icons = {
            success: 'fa-check-circle',
            danger:  'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info:    'fa-info-circle'
        };

        var toastHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show position-fixed shadow" ' +
            'style="top:80px;right:20px;z-index:9999;min-width:300px;max-width:450px;" role="alert">' +
            '<i class="fas ' + (icons[type] || 'fa-info-circle') + ' me-2"></i>' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>';

        var $toast = $(toastHtml).appendTo('body');

        setTimeout(function() {
            $toast.fadeOut(400, function() { $(this).remove(); });
        }, 5000);
    }

    // ========================================================
    // UTILITY: Escape HTML for safe insertion
    // ========================================================
    function escapeHtml(text) {
        if (!text) return '';
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // ========================================================
    // GANTT CONTAINER RESIZE (drag bottom edge)
    // ========================================================
    var ganttContainer = document.getElementById('ganttContainer');
    if (ganttContainer) {
        var startY, startHeight;

        function onMouseDown(e) {
            if (e.offsetY > ganttContainer.offsetHeight - 6) {
                startY = e.clientY;
                startHeight = ganttContainer.offsetHeight;
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
                e.preventDefault();
            }
        }

        function onMouseMove(e) {
            var newHeight = startHeight + (e.clientY - startY);
            if (newHeight >= 200 && newHeight <= 1200) {
                ganttContainer.style.height = newHeight + 'px';
            }
        }

        function onMouseUp() {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        }

        ganttContainer.addEventListener('mousedown', onMouseDown);
        ganttContainer.style.cursor = 'default';
    }

});
</script>
@endpush
