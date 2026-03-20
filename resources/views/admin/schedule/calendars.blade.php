@extends('layouts.admin')

@section('title', 'Work Calendars')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 font-weight-bold text-gray-800">
                <i class="fas fa-clock me-2 text-primary"></i>Work Calendars
            </h4>
            <p class="text-muted mb-0">Define work weeks, hours, holidays, and shutdowns for accurate schedule calculations.</p>
        </div>
        <div>
            <a href="{{ route('admin.schedule.index') }}" class="btn btn-outline-secondary btn-sm me-1">
                <i class="fas fa-arrow-left me-1"></i> Back to Schedules
            </a>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#calendarModal" onclick="openCalendarModal()">
                <i class="fas fa-plus me-1"></i> New Calendar
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    <div id="ajax-alert" class="alert alert-dismissible fade show d-none" role="alert">
        <span id="ajax-alert-msg"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    {{-- Calendar Cards --}}
    @forelse($calendars as $calendar)
        @php
            $workDays = $calendar->getWorkDaysArray();
            $allDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $exceptionCount = $calendar->exceptions_count ?? $calendar->exceptions->count();
        @endphp
        <div class="card shadow mb-4" id="calendar-card-{{ $calendar->cal_id }}">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar me-1"></i> {{ $calendar->cal_name }}
                    </h6>
                    @if($calendar->cal_is_default)
                        <span class="badge bg-success ms-2">Default</span>
                    @endif
                    @if($calendar->cal_status != 1)
                        <span class="badge bg-secondary ms-2">Inactive</span>
                    @endif
                </div>
                <div>
                    <button type="button"
                            class="btn btn-sm btn-outline-primary"
                            title="Edit Calendar"
                            onclick="openCalendarModal({{ json_encode($calendar) }})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button"
                            class="btn btn-sm btn-outline-danger"
                            title="Delete Calendar"
                            onclick="deleteCalendar({{ $calendar->cal_id }}, '{{ addslashes($calendar->cal_name) }}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Calendar Details --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted text-uppercase fw-bold">Timezone</small>
                            <div>{{ $calendar->cal_timezone ?? 'Not set' }}</div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted text-uppercase fw-bold">Work Days</small>
                            <div class="mt-1">
                                @foreach($allDays as $day)
                                    @if(in_array($day, $workDays))
                                        <span class="badge bg-primary me-1">{{ $day }}</span>
                                    @else
                                        <span class="badge bg-light text-muted border me-1">{{ $day }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted text-uppercase fw-bold">Work Hours</small>
                            <div>
                                <i class="fas fa-sun text-warning me-1"></i>
                                {{ $calendar->cal_work_start ? \Carbon\Carbon::parse($calendar->cal_work_start)->format('g:i A') : '7:00 AM' }}
                                <i class="fas fa-arrow-right mx-1 text-muted small"></i>
                                <i class="fas fa-moon text-info me-1"></i>
                                {{ $calendar->cal_work_end ? \Carbon\Carbon::parse($calendar->cal_work_end)->format('g:i A') : '3:30 PM' }}
                            </div>
                        </div>
                    </div>

                    {{-- Exceptions Summary --}}
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <small class="text-muted text-uppercase fw-bold">Exceptions</small>
                                <span class="badge bg-info ms-1">{{ $exceptionCount }}</span>
                            </div>
                            <div>
                                <button type="button"
                                        class="btn btn-sm btn-outline-info"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#exceptions-{{ $calendar->cal_id }}"
                                        aria-expanded="false"
                                        title="Toggle exceptions list">
                                    <i class="fas fa-chevron-down me-1"></i> Show
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-outline-success"
                                        onclick="openExceptionModal({{ $calendar->cal_id }}, '{{ addslashes($calendar->cal_name) }}')"
                                        title="Add Exception">
                                    <i class="fas fa-plus me-1"></i> Add
                                </button>
                            </div>
                        </div>

                        <div class="collapse" id="exceptions-{{ $calendar->cal_id }}">
                            @if($calendar->exceptions->isEmpty())
                                <p class="text-muted fst-italic small mb-0">No exceptions defined.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Name</th>
                                                <th>Hours</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($calendar->exceptions->sortBy('cex_date') as $exception)
                                                @php
                                                    $typeBadges = [
                                                        'holiday'  => 'danger',
                                                        'shutdown' => 'dark',
                                                        'workday'  => 'success',
                                                    ];
                                                    $typeBg = $typeBadges[$exception->cex_type] ?? 'secondary';
                                                @endphp
                                                <tr id="exception-row-{{ $exception->cex_id }}">
                                                    <td>{{ $exception->cex_date->format('m/d/Y') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $typeBg }}">
                                                            {{ ucfirst($exception->cex_type) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $exception->cex_name ?? '---' }}</td>
                                                    <td>
                                                        @if($exception->cex_type === 'workday' && $exception->cex_work_start)
                                                            {{ \Carbon\Carbon::parse($exception->cex_work_start)->format('g:i A') }}
                                                            - {{ \Carbon\Carbon::parse($exception->cex_work_end)->format('g:i A') }}
                                                        @else
                                                            <span class="text-muted">&mdash;</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button"
                                                                class="btn btn-sm btn-outline-danger p-0 px-1"
                                                                title="Delete Exception"
                                                                onclick="deleteException({{ $exception->cex_id }}, {{ $calendar->cal_id }})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Work Calendars Defined</h5>
                <p class="text-muted mb-3">Create a work calendar to define work weeks, hours, and exceptions for schedule calculations.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#calendarModal" onclick="openCalendarModal()">
                    <i class="fas fa-plus me-1"></i> Create First Calendar
                </button>
            </div>
        </div>
    @endforelse

</div>

{{-- Add/Edit Calendar Modal --}}
<div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="calendarModalLabel">
                    <i class="fas fa-calendar-plus me-1"></i> <span id="calendarModalTitle">New Calendar</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="calendarForm">
                <div class="modal-body">
                    <input type="hidden" id="cal_id" name="cal_id" value="">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cal_name" class="form-label">Calendar Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cal_name" name="cal_name" required
                                   placeholder="e.g., Standard Work Week">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cal_timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="cal_timezone" name="cal_timezone">
                                <option value="America/New_York">Eastern (America/New_York)</option>
                                <option value="America/Chicago">Central (America/Chicago)</option>
                                <option value="America/Denver">Mountain (America/Denver)</option>
                                <option value="America/Phoenix">Arizona (America/Phoenix)</option>
                                <option value="America/Los_Angeles">Pacific (America/Los_Angeles)</option>
                                <option value="America/Anchorage">Alaska (America/Anchorage)</option>
                                <option value="Pacific/Honolulu">Hawaii (Pacific/Honolulu)</option>
                                <option value="UTC">UTC</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Work Days <span class="text-danger">*</span></label>
                        <div class="d-flex flex-wrap gap-3">
                            @php $daysFull = ['Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednesday', 'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday', 'Sun' => 'Sunday']; @endphp
                            @foreach($daysFull as $abbr => $full)
                                <div class="form-check">
                                    <input class="form-check-input work-day-check" type="checkbox"
                                           id="day_{{ $abbr }}" value="{{ $abbr }}"
                                           {{ in_array($abbr, ['Mon','Tue','Wed','Thu','Fri']) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="day_{{ $abbr }}">{{ $full }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="cal_work_start" class="form-label">Work Start</label>
                            <input type="time" class="form-control" id="cal_work_start" name="cal_work_start" value="07:00">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cal_work_end" class="form-label">Work End</label>
                            <input type="time" class="form-control" id="cal_work_end" name="cal_work_end" value="15:30">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Default Calendar</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="cal_is_default" name="cal_is_default">
                                <label class="form-check-label" for="cal_is_default">Set as default</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="calendarSaveBtn">
                        <i class="fas fa-save me-1"></i> Save Calendar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Exception Modal --}}
<div class="modal fade" id="exceptionModal" tabindex="-1" aria-labelledby="exceptionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exceptionModalLabel">
                    <i class="fas fa-calendar-day me-1"></i> Add Exception
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exceptionForm">
                <div class="modal-body">
                    <input type="hidden" id="exc_calendar_id" name="cex_calendar_id" value="">
                    <p class="text-muted mb-3">
                        Adding exception for: <strong id="exc_calendar_name"></strong>
                    </p>

                    <div class="mb-3">
                        <label for="cex_date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="cex_date" name="cex_date" required>
                    </div>

                    <div class="mb-3">
                        <label for="cex_type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="cex_type" name="cex_type" required>
                            <option value="holiday">Holiday</option>
                            <option value="shutdown">Shutdown</option>
                            <option value="workday">Special Workday</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="cex_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="cex_name" name="cex_name"
                               placeholder="e.g., Labor Day, Winter Shutdown">
                    </div>

                    <div id="workday-hours-section" class="d-none">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cex_work_start" class="form-label">Custom Work Start</label>
                                <input type="time" class="form-control" id="cex_work_start" name="cex_work_start" value="07:00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cex_work_end" class="form-label">Custom Work End</label>
                                <input type="time" class="form-control" id="cex_work_end" name="cex_work_end" value="15:30">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="exceptionSaveBtn">
                        <i class="fas fa-save me-1"></i> Save Exception
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-1"></i> Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="deleteConfirmMsg">Are you sure you want to delete this item?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="deleteConfirmBtn">
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
    // Toggle workday hours visibility
    $('#cex_type').on('change', function() {
        if ($(this).val() === 'workday') {
            $('#workday-hours-section').removeClass('d-none');
        } else {
            $('#workday-hours-section').addClass('d-none');
        }
    });
});

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
 * Open the Calendar modal for creating or editing.
 */
function openCalendarModal(calendar) {
    var form = $('#calendarForm')[0];
    form.reset();

    if (calendar) {
        // Edit mode
        $('#calendarModalTitle').text('Edit Calendar');
        $('#cal_id').val(calendar.cal_id);
        $('#cal_name').val(calendar.cal_name);
        $('#cal_timezone').val(calendar.cal_timezone || 'America/New_York');
        $('#cal_work_start').val(calendar.cal_work_start || '07:00');
        $('#cal_work_end').val(calendar.cal_work_end || '15:30');
        $('#cal_is_default').prop('checked', calendar.cal_is_default == 1);

        // Set work day checkboxes
        var workDays = calendar.cal_work_week ? calendar.cal_work_week.split(',').map(function(d) { return d.trim(); }) : [];
        $('.work-day-check').each(function() {
            $(this).prop('checked', workDays.indexOf($(this).val()) !== -1);
        });
    } else {
        // Create mode
        $('#calendarModalTitle').text('New Calendar');
        $('#cal_id').val('');
        $('#cal_timezone').val('America/New_York');
        $('#cal_work_start').val('07:00');
        $('#cal_work_end').val('15:30');
        $('#cal_is_default').prop('checked', false);

        // Default Mon-Fri checked
        $('.work-day-check').each(function() {
            var defaults = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
            $(this).prop('checked', defaults.indexOf($(this).val()) !== -1);
        });
    }

    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('calendarModal'));
    modal.show();
}

/**
 * Save calendar via AJAX (create or update).
 */
$('#calendarForm').on('submit', function(e) {
    e.preventDefault();

    var calId = $('#cal_id').val();
    var workDays = [];
    $('.work-day-check:checked').each(function() {
        workDays.push($(this).val());
    });

    if (workDays.length === 0) {
        showAlert('Please select at least one work day.', 'warning');
        return;
    }

    var data = {
        cal_name: $('#cal_name').val(),
        cal_timezone: $('#cal_timezone').val(),
        cal_work_week: workDays.join(','),
        cal_work_start: $('#cal_work_start').val(),
        cal_work_end: $('#cal_work_end').val(),
        cal_is_default: $('#cal_is_default').is(':checked') ? 1 : 0
    };

    var url, method;
    if (calId) {
        url = '{{ url("admin/schedule/calendars") }}/' + calId;
        method = 'PUT';
    } else {
        url = '{{ route("admin.schedule.storeCalendar") }}';
        method = 'POST';
    }

    var $btn = $('#calendarSaveBtn');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

    $.ajax({
        url: url,
        type: method,
        data: data,
        success: function(response) {
            bootstrap.Modal.getInstance(document.getElementById('calendarModal')).hide();
            showAlert(response.message || 'Calendar saved successfully.', 'success');
            setTimeout(function() { location.reload(); }, 800);
        },
        error: function(xhr) {
            var msg = 'An error occurred.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
            }
            showAlert(msg, 'danger');
        },
        complete: function() {
            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Calendar');
        }
    });
});

/**
 * Open the exception modal for a specific calendar.
 */
function openExceptionModal(calendarId, calendarName) {
    var form = $('#exceptionForm')[0];
    form.reset();

    $('#exc_calendar_id').val(calendarId);
    $('#exc_calendar_name').text(calendarName);
    $('#cex_type').val('holiday').trigger('change');

    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('exceptionModal'));
    modal.show();
}

/**
 * Save exception via AJAX.
 */
$('#exceptionForm').on('submit', function(e) {
    e.preventDefault();

    var calendarId = $('#exc_calendar_id').val();
    var data = {
        cex_calendar_id: calendarId,
        cex_date: $('#cex_date').val(),
        cex_type: $('#cex_type').val(),
        cex_name: $('#cex_name').val()
    };

    if (data.cex_type === 'workday') {
        data.cex_work_start = $('#cex_work_start').val();
        data.cex_work_end = $('#cex_work_end').val();
    }

    if (!data.cex_date) {
        showAlert('Please select a date.', 'warning');
        return;
    }

    var $btn = $('#exceptionSaveBtn');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

    $.ajax({
        url: '{{ route("admin.schedule.storeCalendarException") }}',
        type: 'POST',
        data: data,
        success: function(response) {
            bootstrap.Modal.getInstance(document.getElementById('exceptionModal')).hide();
            showAlert(response.message || 'Exception added successfully.', 'success');
            setTimeout(function() { location.reload(); }, 800);
        },
        error: function(xhr) {
            var msg = 'An error occurred.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                msg = Object.values(xhr.responseJSON.errors).flat().join(' ');
            }
            showAlert(msg, 'danger');
        },
        complete: function() {
            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Exception');
        }
    });
});

/**
 * Delete a calendar with confirmation.
 */
function deleteCalendar(calendarId, calendarName) {
    $('#deleteConfirmMsg').text('Are you sure you want to delete the calendar "' + calendarName + '"? This will also remove all its exceptions.');

    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteConfirmModal'));
    modal.show();

    $('#deleteConfirmBtn').off('click').on('click', function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Deleting...');

        $.ajax({
            url: '{{ url("admin/schedule/calendars") }}/' + calendarId,
            type: 'DELETE',
            success: function(response) {
                modal.hide();
                $('#calendar-card-' + calendarId).fadeOut(400, function() { $(this).remove(); });
                showAlert(response.message || 'Calendar deleted.', 'success');
            },
            error: function(xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete calendar.';
                showAlert(msg, 'danger');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-trash me-1"></i> Delete');
            }
        });
    });
}

/**
 * Delete a calendar exception.
 */
function deleteException(exceptionId, calendarId) {
    if (!confirm('Delete this exception?')) return;

    $.ajax({
        url: '{{ url("admin/schedule/calendar-exceptions") }}/' + exceptionId,
        type: 'DELETE',
        success: function(response) {
            $('#exception-row-' + exceptionId).fadeOut(300, function() { $(this).remove(); });
            showAlert(response.message || 'Exception deleted.', 'success');
        },
        error: function(xhr) {
            var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to delete exception.';
            showAlert(msg, 'danger');
        }
    });
}
</script>
@endpush
