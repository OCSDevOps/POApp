@extends('layouts.admin')

@section('title', 'Sync Log Details')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt"></i> Sync Log &mdash; {{ ucfirst(str_replace('_', ' ', $log->sync_type)) }}
        </h1>
        <a href="{{ route('admin.procore.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    {{-- Details Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle me-1"></i> Sync Details
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th class="text-muted" style="width: 40%;">Type</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $log->sync_type)) }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Status</th>
                            <td>
                                @if($log->sync_status === 'completed')
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Completed</span>
                                @elseif($log->sync_status === 'in_progress')
                                    <span class="badge bg-warning"><i class="fas fa-spinner fa-spin me-1"></i> In Progress</span>
                                @elseif($log->sync_status === 'failed')
                                    <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i> Failed</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($log->sync_status) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Started</th>
                            <td>{{ \Carbon\Carbon::parse($log->sync_started_at)->format('M d, Y h:i:s A') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Completed</th>
                            <td>
                                @if($log->sync_completed_at)
                                    {{ \Carbon\Carbon::parse($log->sync_completed_at)->format('M d, Y h:i:s A') }}
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th class="text-muted" style="width: 40%;">Duration</th>
                            <td>
                                @if($log->sync_started_at && $log->sync_completed_at)
                                    @php
                                        $started = \Carbon\Carbon::parse($log->sync_started_at);
                                        $completed = \Carbon\Carbon::parse($log->sync_completed_at);
                                        $diffInSeconds = $started->diffInSeconds($completed);
                                    @endphp
                                    @if($diffInSeconds < 60)
                                        {{ $diffInSeconds }} seconds
                                    @elseif($diffInSeconds < 3600)
                                        {{ floor($diffInSeconds / 60) }}m {{ $diffInSeconds % 60 }}s
                                    @else
                                        {{ floor($diffInSeconds / 3600) }}h {{ floor(($diffInSeconds % 3600) / 60) }}m
                                    @endif
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Items Synced</th>
                            <td>
                                <span class="badge bg-success">{{ $log->items_synced ?? 0 }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted">Items Failed</th>
                            <td>
                                <span class="badge bg-{{ ($log->items_failed ?? 0) > 0 ? 'danger' : 'secondary' }}">
                                    {{ $log->items_failed ?? 0 }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Error Message --}}
    @if($log->error_message)
        <div class="alert alert-danger shadow-sm">
            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-1"></i> Error Message</h6>
            <p class="mb-0">{{ $log->error_message }}</p>
        </div>
    @endif

    {{-- Sync Details (JSON) --}}
    @if($log->sync_details)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <a class="text-decoration-none" data-bs-toggle="collapse" href="#syncDetailsCollapse" role="button" aria-expanded="false" aria-controls="syncDetailsCollapse">
                        <i class="fas fa-chevron-down me-1"></i> Sync Details (JSON)
                    </a>
                </h6>
            </div>
            <div class="collapse" id="syncDetailsCollapse">
                <div class="card-body">
                    <pre class="bg-light p-3 rounded" style="max-height: 500px; overflow-y: auto;"><code>{{ json_encode(is_string($log->sync_details) ? json_decode($log->sync_details, true) : $log->sync_details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // No additional scripts needed for this view
</script>
@endpush
