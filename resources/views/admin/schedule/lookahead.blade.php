@extends('layouts.admin')

@section('title', 'Lookahead Plan - ' . $project->proj_name)

@push('styles')
<style>
    .lookahead-ready {
        background-color: #f0fdf4 !important;
    }
    .lookahead-blocked {
        background-color: #fef2f2 !important;
    }
    .week-header {
        background-color: #e2e8f0;
        font-weight: 700;
        font-size: 0.9rem;
    }
    .constraint-card {
        border-left: 4px solid;
        transition: border-color 0.2s;
    }
    .constraint-card.urgency-past { border-left-color: #e74a3b; }
    .constraint-card.urgency-thisweek { border-left-color: #f6c23e; }
    .constraint-card.urgency-future { border-left-color: #1cc88a; }
    .constraint-needed-by.text-past { color: #e74a3b; font-weight: 700; }
    .constraint-needed-by.text-thisweek { color: #f6c23e; font-weight: 600; }
    .constraint-needed-by.text-future { color: #1cc88a; }
    .driver-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        color: #fff;
        flex-shrink: 0;
    }
    .driver-icon.permit { background-color: #6f42c1; }
    .driver-icon.inspection { background-color: #0dcaf0; }
    .driver-icon.procurement { background-color: #fd7e14; }
    .driver-icon.access_window { background-color: #20c997; }
    .driver-icon.utility_cutover { background-color: #d63384; }
    .driver-icon.owner_decision { background-color: #6610f2; }
    .driver-icon.default { background-color: #6c757d; }
    .blocking-reason {
        font-size: 0.78rem;
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 font-weight-bold text-gray-800">
                <i class="fas fa-binoculars me-2 text-primary"></i>Lookahead Plan: {{ $project->proj_name }}
            </h4>
            <p class="text-muted mb-0">
                Last Planner-style rolling lookahead window.
                @if($project->proj_number)
                    Project #{{ $project->proj_number }}
                @endif
            </p>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.schedule.show', $project->proj_id) }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Schedule
            </a>
        </div>
    </div>

    {{-- Week Selector --}}
    <div class="card shadow mb-4">
        <div class="card-body py-2">
            <div class="d-flex align-items-center">
                <span class="text-muted me-3 fw-semibold">
                    <i class="fas fa-calendar-week me-1"></i> Planning Window:
                </span>
                @php
                    $weekOptions = [2, 3, 4, 6];
                    $currentWeeks = $weeks ?? 3;
                @endphp
                <div class="btn-group" role="group">
                    @foreach($weekOptions as $w)
                        <a href="{{ route('admin.schedule.lookahead', ['projectId' => $project->proj_id, 'weeks' => $w]) }}"
                           class="btn btn-sm {{ $currentWeeks == $w ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ $w }} Weeks
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Flash alert for AJAX --}}
    <div id="ajax-alert" class="alert alert-dismissible fade show d-none" role="alert">
        <span id="ajax-alert-msg"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    {{-- Main Content: Two Column Layout (stacked on mobile) --}}
    <div class="row">

        {{-- LEFT: Lookahead Activities --}}
        <div class="col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tasks me-1"></i> Lookahead Activities
                    </h6>
                    <span class="badge bg-secondary">{{ $currentWeeks }}-Week Window</span>
                </div>
                <div class="card-body p-0">
                    @if(!empty($lookaheadData) && count($lookaheadData) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0" id="lookaheadTable">
                                <thead>
                                    <tr>
                                        <th>Activity</th>
                                        <th class="text-center" style="width: 80px;">Duration</th>
                                        <th class="text-center" style="width: 110px;">Sched. Start</th>
                                        <th class="text-center" style="width: 100px;">Status</th>
                                        <th class="text-center" style="width: 70px;">Ready?</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lookaheadData as $weekGroup)
                                        {{-- Week header row --}}
                                        <tr class="week-header">
                                            <td colspan="5">
                                                <i class="fas fa-calendar-week me-1"></i>
                                                Week of {{ \Carbon\Carbon::parse($weekGroup['week_start'])->format('M d, Y') }}
                                                <span class="badge bg-dark ms-1">{{ count($weekGroup['activities'] ?? []) }} activities</span>
                                            </td>
                                        </tr>

                                        @forelse($weekGroup['activities'] ?? [] as $activity)
                                            @php
                                                $isReady = $activity['is_ready'] ?? false;
                                                $blockingReason = $activity['blocking_reason'] ?? null;
                                                $status = $activity['status'] ?? 'NOT_STARTED';
                                                $statusBadges = [
                                                    'NOT_STARTED' => 'secondary',
                                                    'IN_PROGRESS' => 'info',
                                                    'COMPLETE'    => 'success',
                                                    'BLOCKED'     => 'danger',
                                                ];
                                                $statusBg = $statusBadges[$status] ?? 'secondary';
                                                $durationDays = $activity['duration_days'] ?? '---';
                                                $schedStart = isset($activity['early_start']) ? \Carbon\Carbon::parse($activity['early_start'])->format('m/d/Y') : '---';
                                            @endphp
                                            <tr class="{{ $isReady ? 'lookahead-ready' : 'lookahead-blocked' }}">
                                                <td>
                                                    <div class="fw-semibold">{{ $activity['name'] ?? 'Unnamed' }}</div>
                                                    @if(!$isReady && $blockingReason)
                                                        <div class="blocking-reason mt-1">
                                                            <i class="fas fa-exclamation-circle me-1"></i>{{ $blockingReason }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{ $durationDays }}{{ is_numeric($durationDays) ? 'd' : '' }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $schedStart }}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $statusBg }}">{{ str_replace('_', ' ', $status) }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($isReady)
                                                        <i class="fas fa-check-circle text-success fs-5" title="Ready to execute"></i>
                                                    @else
                                                        <i class="fas fa-times-circle text-danger fs-5" title="{{ $blockingReason ?? 'Not ready' }}"></i>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3 fst-italic">
                                                    No activities scheduled this week.
                                                </td>
                                            </tr>
                                        @endforelse
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Lookahead Data</h5>
                            <p class="text-muted mb-0">Run a CPM schedule calculation first to generate lookahead activities.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT: Constraint Log --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shield-alt me-1"></i> Constraint Log
                    </h6>
                    <span class="badge bg-{{ !empty($constraintLog) ? 'warning' : 'secondary' }}">
                        {{ !empty($constraintLog) ? count($constraintLog) : 0 }} constraints
                    </span>
                </div>
                <div class="card-body">
                    @if(!empty($constraintLog) && count($constraintLog) > 0)
                        @foreach($constraintLog as $constraint)
                            @php
                                $driverType = strtolower($constraint['driver_type'] ?? 'default');
                                $driverIcons = [
                                    'permit'          => 'fa-clipboard-list',
                                    'inspection'      => 'fa-search',
                                    'procurement'     => 'fa-truck',
                                    'access_window'   => 'fa-door-open',
                                    'utility_cutover' => 'fa-bolt',
                                    'owner_decision'  => 'fa-gavel',
                                ];
                                $driverIcon = $driverIcons[$driverType] ?? 'fa-flag';

                                $neededBy = isset($constraint['needed_by_date']) ? \Carbon\Carbon::parse($constraint['needed_by_date']) : null;
                                $today = \Carbon\Carbon::today();
                                $endOfWeek = $today->copy()->endOfWeek();

                                if (!$neededBy) {
                                    $urgencyClass = 'future';
                                } elseif ($neededBy->lt($today)) {
                                    $urgencyClass = 'past';
                                } elseif ($neededBy->lte($endOfWeek)) {
                                    $urgencyClass = 'thisweek';
                                } else {
                                    $urgencyClass = 'future';
                                }

                                $clStatus = strtoupper($constraint['status'] ?? 'OPEN');
                                $clStatusBadges = [
                                    'OPEN'        => 'warning',
                                    'IN_PROGRESS' => 'info',
                                    'CLEARED'     => 'success',
                                ];
                                $clStatusBg = $clStatusBadges[$clStatus] ?? 'secondary';

                                $constraintId = $constraint['id'] ?? $constraint['cl_id'] ?? null;
                                $driverId = $constraint['driver_id'] ?? $constraint['drv_id'] ?? null;
                            @endphp
                            <div class="card constraint-card urgency-{{ $urgencyClass }} mb-3" id="constraint-{{ $constraintId }}">
                                <div class="card-body py-3 px-3">
                                    <div class="d-flex align-items-start">
                                        <div class="driver-icon {{ $driverType }} me-3" title="{{ ucfirst(str_replace('_', ' ', $driverType)) }}">
                                            <i class="fas {{ $driverIcon }}"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <div>
                                                    <strong>{{ $constraint['driver_name'] ?? 'Unknown Driver' }}</strong>
                                                    <span class="badge bg-{{ $clStatusBg }} ms-1">{{ $clStatus }}</span>
                                                </div>
                                            </div>

                                            <div class="small text-muted mb-1">
                                                <i class="fas fa-link me-1"></i>
                                                Activity: <em>{{ $constraint['activity_name'] ?? '---' }}</em>
                                            </div>

                                            @if($neededBy)
                                                <div class="small mb-1">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Needed by:
                                                    <span class="constraint-needed-by text-{{ $urgencyClass }}">
                                                        {{ $neededBy->format('m/d/Y') }}
                                                        @if($urgencyClass === 'past')
                                                            (OVERDUE)
                                                        @elseif($urgencyClass === 'thisweek')
                                                            (This Week)
                                                        @endif
                                                    </span>
                                                </div>
                                            @endif

                                            @if(!empty($constraint['owner_role']))
                                                <div class="small text-muted mb-2">
                                                    <i class="fas fa-user me-1"></i>
                                                    Owner: {{ $constraint['owner_role'] }}
                                                </div>
                                            @endif

                                            {{-- Quick action buttons (only for non-CLEARED items) --}}
                                            @if($clStatus !== 'CLEARED' && $driverId)
                                                <div class="mt-2">
                                                    @if($clStatus === 'OPEN')
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-info me-1 constraint-status-btn"
                                                                data-driver-id="{{ $driverId }}"
                                                                data-constraint-id="{{ $constraintId }}"
                                                                data-new-status="IN_PROGRESS">
                                                            <i class="fas fa-play me-1"></i> Mark In Progress
                                                        </button>
                                                    @endif
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-success constraint-status-btn"
                                                            data-driver-id="{{ $driverId }}"
                                                            data-constraint-id="{{ $constraintId }}"
                                                            data-new-status="CLEARED">
                                                        <i class="fas fa-check me-1"></i> Mark Cleared
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-check-double fa-3x text-success mb-3"></i>
                            <h5 class="text-muted">No Open Constraints</h5>
                            <p class="text-muted mb-0">All constraints are cleared or none have been logged.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>{{-- /.row --}}

    {{-- Legend --}}
    <div class="card shadow mb-4">
        <div class="card-body py-2">
            <div class="d-flex flex-wrap align-items-center gap-4">
                <small class="text-muted fw-bold text-uppercase">Legend:</small>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block me-1" style="width:16px;height:16px;background:#f0fdf4;border:1px solid #86efac;border-radius:3px;"></span>
                    <small>Ready</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block me-1" style="width:16px;height:16px;background:#fef2f2;border:1px solid #fca5a5;border-radius:3px;"></span>
                    <small>Blocked</small>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <small class="text-muted">|</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block me-1" style="width:16px;height:4px;background:#e74a3b;border-radius:2px;"></span>
                    <small>Overdue</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block me-1" style="width:16px;height:4px;background:#f6c23e;border-radius:2px;"></span>
                    <small>Due This Week</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="d-inline-block me-1" style="width:16px;height:4px;background:#1cc88a;border-radius:2px;"></span>
                    <small>Future</small>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <small class="text-muted">|</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="driver-icon permit me-1" style="width:20px;height:20px;font-size:0.6rem;"><i class="fas fa-clipboard-list"></i></span>
                    <small>Permit</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="driver-icon inspection me-1" style="width:20px;height:20px;font-size:0.6rem;"><i class="fas fa-search"></i></span>
                    <small>Inspection</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="driver-icon procurement me-1" style="width:20px;height:20px;font-size:0.6rem;"><i class="fas fa-truck"></i></span>
                    <small>Procurement</small>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {

    /**
     * Show a flash alert message.
     */
    function showAlert(msg, type) {
        var $alert = $('#ajax-alert');
        $alert.removeClass('d-none alert-success alert-danger alert-warning alert-info')
              .addClass('alert-' + type)
              .find('#ajax-alert-msg').text(msg);
        $alert.addClass('show');
        setTimeout(function() { $alert.addClass('d-none'); }, 5000);
    }

    /**
     * Handle constraint status update buttons.
     */
    $(document).on('click', '.constraint-status-btn', function() {
        var $btn = $(this);
        var driverId = $btn.data('driver-id');
        var constraintId = $btn.data('constraint-id');
        var newStatus = $btn.data('new-status');

        if (!driverId) {
            showAlert('Missing driver reference.', 'danger');
            return;
        }

        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '{{ url("admin/schedule/drivers") }}/' + driverId + '/status',
            type: 'POST',
            data: {
                status: newStatus,
                constraint_id: constraintId
            },
            success: function(response) {
                showAlert(response.message || 'Status updated to ' + newStatus + '.', 'success');

                var $card = $btn.closest('.constraint-card');

                // Update the status badge
                var badgeMap = {
                    'OPEN': 'warning',
                    'IN_PROGRESS': 'info',
                    'CLEARED': 'success'
                };
                $card.find('.badge').first()
                     .removeClass('bg-warning bg-info bg-success bg-secondary bg-danger')
                     .addClass('bg-' + (badgeMap[newStatus] || 'secondary'))
                     .text(newStatus);

                // Update action buttons
                var $actionArea = $btn.closest('.mt-2');
                if (newStatus === 'CLEARED') {
                    $actionArea.fadeOut(300, function() { $(this).remove(); });
                    // Update urgency style to indicate cleared
                    $card.removeClass('urgency-past urgency-thisweek urgency-future')
                         .css('opacity', '0.6');
                } else if (newStatus === 'IN_PROGRESS') {
                    // Remove the "Mark In Progress" button, keep "Mark Cleared"
                    $btn.remove();
                }
            },
            error: function(xhr) {
                var msg = 'Failed to update status.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showAlert(msg, 'danger');
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

});
</script>
@endpush
