@extends('layouts.admin')

@section('title', 'Audit Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Audit Logs</h1>
    <a href="{{ route('admin.security.2fa') }}" class="btn btn-outline-primary">
        <i class="fas fa-shield-alt me-1"></i> 2FA Settings
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-filter me-1"></i> Filters
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.security.audit-logs') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="event_type" class="form-label">Event Type</label>
                    <input
                        type="text"
                        class="form-control"
                        id="event_type"
                        name="event_type"
                        value="{{ $filters['event_type'] ?? '' }}"
                        placeholder="auth.login_success"
                    >
                </div>
                <div class="col-md-3">
                    <label for="user_id" class="form-label">User</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ (string)($filters['user_id'] ?? '') === (string)$u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Apply
                    </button>
                    <a href="{{ route('admin.security.audit-logs') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-history me-1"></i> Activity
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 180px;">Timestamp</th>
                    <th style="width: 180px;">Event</th>
                    <th style="width: 180px;">User</th>
                    <th style="width: 80px;">Company</th>
                    <th style="width: 220px;">Model</th>
                    <th style="width: 120px;">IP</th>
                    <th>Change Summary</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                        <td><code>{{ $log->event_type }}</code></td>
                        <td>
                            @if($log->user)
                                {{ $log->user->name }}<br>
                                <small class="text-muted">{{ $log->user->email }}</small>
                            @else
                                <span class="text-muted">System/Unknown</span>
                            @endif
                        </td>
                        <td>{{ $log->company_id ?? '-' }}</td>
                        <td>
                            @if($log->auditable_type)
                                <span class="small">{{ class_basename($log->auditable_type) }}</span>
                                <span class="text-muted">#{{ $log->auditable_id }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $log->ip_address ?? '-' }}</td>
                        <td>
                            @php
                                $old = $log->old_values_decoded;
                                $new = $log->new_values_decoded;
                            @endphp
                            @if(!empty($old) || !empty($new))
                                <details>
                                    <summary class="small">View payload</summary>
                                    <pre class="small bg-light p-2 mt-2 mb-0">{{ json_encode(['old' => $old, 'new' => $new], JSON_PRETTY_PRINT) }}</pre>
                                </details>
                            @else
                                <span class="text-muted small">No field payload</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No audit records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="card-footer">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
