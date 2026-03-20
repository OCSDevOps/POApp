@extends('layouts.admin')

@section('title', 'Procore Integration')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plug"></i> Procore Integration
        </h1>
        <a href="{{ route('admin.procore.settings') }}" class="btn btn-outline-secondary">
            <i class="fas fa-cog"></i> Settings
        </a>
    </div>

    {{-- Last Sync Status Card --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="text-uppercase text-muted mb-1">Last Sync</h6>
                    <h4 class="mb-0">
                        @if($lastSync)
                            {{ \Carbon\Carbon::parse($lastSync->sync_started_at)->format('M d, Y h:i A') }}
                        @else
                            Never
                        @endif
                    </h4>
                </div>
                <div class="col-md-6 text-md-end">
                    @if($lastSync)
                        @if($lastSync->sync_status === 'completed')
                            <span class="badge bg-success fs-6"><i class="fas fa-check-circle me-1"></i> Completed</span>
                        @elseif($lastSync->sync_status === 'in_progress')
                            <span class="badge bg-warning fs-6"><i class="fas fa-spinner fa-spin me-1"></i> In Progress</span>
                        @elseif($lastSync->sync_status === 'failed')
                            <span class="badge bg-danger fs-6"><i class="fas fa-times-circle me-1"></i> Failed</span>
                        @else
                            <span class="badge bg-secondary fs-6">{{ ucfirst($lastSync->sync_status) }}</span>
                        @endif
                    @else
                        <span class="badge bg-secondary fs-6">No syncs yet</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="row mb-4">
        <div class="col-md-3 mb-2">
            <form method="POST" action="{{ route('admin.procore.syncall') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-sync-alt me-1"></i> Sync All
                </button>
            </form>
        </div>
        <div class="col-md-3 mb-2">
            <form method="POST" action="{{ route('admin.procore.syncprojects') }}">
                @csrf
                <button type="submit" class="btn btn-info w-100 text-white">
                    <i class="fas fa-project-diagram me-1"></i> Sync Projects
                </button>
            </form>
        </div>
        <div class="col-md-3 mb-2">
            <form method="POST" action="{{ route('admin.procore.syncvendors') }}">
                @csrf
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-truck me-1"></i> Sync Vendors
                </button>
            </form>
        </div>
        <div class="col-md-3 mb-2">
            <form method="POST" action="{{ route('admin.procore.synccostcodes') }}">
                @csrf
                <button type="submit" class="btn btn-warning w-100">
                    <i class="fas fa-code me-1"></i> Sync Cost Codes
                </button>
            </form>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow stat-card primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Project Mappings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $projectMappings->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.procore.projectmappings') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow stat-card info">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Cost Code Mappings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $costCodeMappingsCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-code fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.procore.costcodemappings') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
    </div>

    {{-- Sync History --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-history me-1"></i> Sync History
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable" id="syncHistoryTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Records Synced</th>
                            <th>Duration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($syncHistory as $log)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($log->sync_started_at)->format('M d, Y h:i A') }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $log->sync_type)) }}</td>
                                <td>
                                    @if($log->sync_status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($log->sync_status === 'in_progress')
                                        <span class="badge bg-warning">In Progress</span>
                                    @elseif($log->sync_status === 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($log->sync_status) }}</span>
                                    @endif
                                </td>
                                <td>{{ $log->items_synced ?? 0 }}</td>
                                <td>
                                    @if($log->sync_completed_at)
                                        {{ \Carbon\Carbon::parse($log->sync_started_at)->diffForHumans($log->sync_completed_at, true) }}
                                    @else
                                        --
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.procore.synclog', $log->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No sync history available.</td>
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
        $('#syncHistoryTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 25,
            responsive: true
        });
    });
</script>
@endpush
