@extends('layouts.admin')

@section('title', 'Accounting Integrations')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-link"></i> Accounting Integrations
        </h1>
        <a href="{{ route('admin.integrations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Integration
        </a>
    </div>

    @if($integrations->isEmpty())
        {{-- Empty State --}}
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-plug fa-4x text-gray-300 mb-3"></i>
                <h4 class="text-muted">No Integrations Configured</h4>
                <p class="text-muted mb-4">
                    Set up your first accounting integration to sync purchase orders, vendors, and items
                    with your accounting software.
                </p>
                <a href="{{ route('admin.integrations.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-1"></i> Set Up Your First Integration
                </a>
            </div>
        </div>
    @else
        {{-- Integration Cards --}}
        <div class="row">
            @foreach($integrations as $integration)
                <div class="col-lg-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                @if($integration->integration_type === 'sage')
                                    <i class="fas fa-building me-1"></i> Sage 300
                                @elseif($integration->integration_type === 'quickbooks')
                                    <i class="fas fa-calculator me-1"></i> QuickBooks Online
                                @else
                                    <i class="fas fa-link me-1"></i> {{ ucfirst($integration->integration_type) }}
                                @endif
                            </h6>
                            <form method="POST" action="{{ route('admin.integrations.toggleactive', $integration->id) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                           role="switch"
                                           id="toggle_{{ $integration->id }}"
                                           {{ $integration->is_active ? 'checked' : '' }}
                                           onchange="this.form.submit()">
                                    <label class="form-check-label" for="toggle_{{ $integration->id }}">
                                        {{ $integration->is_active ? 'Active' : 'Inactive' }}
                                    </label>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            {{-- Integration Info --}}
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <small class="text-muted d-block">Company</small>
                                        <strong>{{ $integration->company->name ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <small class="text-muted d-block">Status</small>
                                        @if($integration->is_active)
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-pause-circle me-1"></i> Inactive</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Auto-Sync Settings --}}
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">Auto-Sync</small>
                                <span class="badge bg-{{ $integration->auto_sync_purchase_orders ? 'info' : 'light text-dark border' }} me-1">
                                    <i class="fas fa-{{ $integration->auto_sync_purchase_orders ? 'check' : 'times' }} me-1"></i> POs
                                </span>
                                <span class="badge bg-{{ $integration->auto_sync_vendors ? 'info' : 'light text-dark border' }} me-1">
                                    <i class="fas fa-{{ $integration->auto_sync_vendors ? 'check' : 'times' }} me-1"></i> Vendors
                                </span>
                                <span class="badge bg-{{ $integration->auto_sync_items ? 'info' : 'light text-dark border' }}">
                                    <i class="fas fa-{{ $integration->auto_sync_items ? 'check' : 'times' }} me-1"></i> Items
                                </span>
                            </div>

                            {{-- Last Sync --}}
                            <div class="mb-3">
                                <small class="text-muted d-block">Last Sync</small>
                                @if($integration->syncLogs && $integration->syncLogs->isNotEmpty())
                                    @php $lastLog = $integration->syncLogs->first(); @endphp
                                    <span class="badge bg-{{ $lastLog->status === 'success' ? 'success' : ($lastLog->status === 'failed' ? 'danger' : 'warning') }} me-1">
                                        {{ ucfirst($lastLog->status) }}
                                    </span>
                                    <small>{{ \Carbon\Carbon::parse($lastLog->started_at)->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">No syncs yet</span>
                                @endif
                            </div>

                            <hr>

                            {{-- Action Buttons --}}
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('admin.integrations.logs', $integration->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-history me-1"></i> View Logs
                                </a>
                                <form method="POST" action="{{ route('admin.integrations.testconnection', $integration->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-satellite-dish me-1"></i> Test Connection
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.integrations.syncpurchaseorders', $integration->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-sync me-1"></i> Sync POs
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.integrations.syncvendors', $integration->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-truck me-1"></i> Sync Vendors
                                    </button>
                                </form>
                                <a href="{{ route('admin.integrations.update', $integration->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-cog me-1"></i> Settings
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                        data-url="{{ route('admin.integrations.destroy', $integration->id) }}" data-name="{{ ucfirst($integration->integration_type) }} integration">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <small>Created {{ $integration->created_at->format('M d, Y') }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@include('partials.delete-modal')
@endsection

@push('scripts')
<script>
    // No additional scripts needed for this view
</script>
@endpush
