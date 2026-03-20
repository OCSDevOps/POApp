@extends('layouts.admin')

@section('title', 'Sync Logs')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-history"></i> Sync Logs &mdash;
            @if($integration->integration_type === 'sage')
                Sage 300
            @elseif($integration->integration_type === 'quickbooks')
                QuickBooks Online
            @else
                {{ ucfirst($integration->integration_type) }}
            @endif
        </h1>
        <a href="{{ route('admin.integrations.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Integrations
        </a>
    </div>

    {{-- Integration Summary --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <small class="text-muted d-block">Type</small>
                    <strong>
                        @if($integration->integration_type === 'sage')
                            <i class="fas fa-building me-1"></i> Sage 300
                        @elseif($integration->integration_type === 'quickbooks')
                            <i class="fas fa-calculator me-1"></i> QuickBooks Online
                        @else
                            <i class="fas fa-link me-1"></i> {{ ucfirst($integration->integration_type) }}
                        @endif
                    </strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Company</small>
                    <strong>{{ $integration->company->name ?? 'N/A' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Status</small>
                    @if($integration->is_active)
                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                    @else
                        <span class="badge bg-secondary"><i class="fas fa-pause-circle me-1"></i> Inactive</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Active Since</small>
                    <strong>{{ optional($integration->created_at)->format('M d, Y') ?? 'N/A' }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Sync Logs Table --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-1"></i> Sync Log Entries
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Entity Type</th>
                            <th>Status</th>
                            <th>Records Synced</th>
                            <th>Records Failed</th>
                            <th>Error Message</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->started_at ? \Carbon\Carbon::parse($log->started_at)->format('M d, Y h:i A') : '—' }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $log->entity_type)) }}</td>
                                <td>
                                    @if($log->status === 'success')
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Success</span>
                                    @elseif($log->status === 'failed')
                                        <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Failed</span>
                                    @elseif($log->status === 'partial')
                                        <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Partial</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($log->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $log->records_succeeded ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ ($log->records_failed ?? 0) > 0 ? 'danger' : 'secondary' }}">
                                        {{ $log->records_failed ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->error_message)
                                        <span title="{{ $log->error_message }}">
                                            {{ \Illuminate\Support\Str::limit($log->error_message, 60) }}
                                        </span>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->started_at && $log->completed_at)
                                        @php
                                            $started = \Carbon\Carbon::parse($log->started_at);
                                            $completed = \Carbon\Carbon::parse($log->completed_at);
                                            $diffInSeconds = $started->diffInSeconds($completed);
                                        @endphp
                                        @if($diffInSeconds < 60)
                                            {{ $diffInSeconds }}s
                                        @elseif($diffInSeconds < 3600)
                                            {{ floor($diffInSeconds / 60) }}m {{ $diffInSeconds % 60 }}s
                                        @else
                                            {{ floor($diffInSeconds / 3600) }}h {{ floor(($diffInSeconds % 3600) / 60) }}m
                                        @endif
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    No sync logs available for this integration.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($logs->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // No additional scripts needed — server-side pagination used
</script>
@endpush
