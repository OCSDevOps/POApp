@extends('layouts.admin')

@section('title', 'Approval Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0">Pending Approvals</h4>
                </div>
                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-warning bg-opacity-10">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $counts->sum() }}</h3>
                                    <small class="text-muted">Total Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary bg-opacity-10">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $counts['budget_co'] ?? 0 }}</h3>
                                    <small class="text-muted">Budget Change Orders</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info bg-opacity-10">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $counts['po_co'] ?? 0 }}</h3>
                                    <small class="text-muted">PO Change Orders</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success bg-opacity-10">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $counts['po'] ?? 0 }}</h3>
                                    <small class="text-muted">Purchase Orders</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="budget_co" {{ request('type') == 'budget_co' ? 'selected' : '' }}>
                                        Budget Change Orders
                                    </option>
                                    <option value="po_co" {{ request('type') == 'po_co' ? 'selected' : '' }}>
                                        PO Change Orders
                                    </option>
                                    <option value="po" {{ request('type') == 'po' ? 'selected' : '' }}>
                                        Purchase Orders
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Approvals List -->
                    @if($pendingApprovals->isEmpty())
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> No pending approvals at this time.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Type</th>
                                        <th>Entity #</th>
                                        <th>Amount</th>
                                        <th>Level</th>
                                        <th>Submitted</th>
                                        <th>Age</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingApprovals as $approval)
                                        @php
                                            $age = $approval->submitted_at ? now()->diffInHours($approval->submitted_at) : 0;
                                            $isOverdue = $age > 48;
                                        @endphp
                                        <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                            <td>
                                                <span class="badge bg-{{ $approval->request_type == 'budget_co' ? 'primary' : ($approval->request_type == 'po_co' ? 'info' : 'secondary') }}">
                                                    @if($approval->request_type == 'budget_co')
                                                        BCO
                                                    @elseif($approval->request_type == 'po_co')
                                                        PCO
                                                    @elseif($approval->request_type == 'po')
                                                        PO
                                                    @else
                                                        {{ strtoupper($approval->request_type) }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ $approval->entity_number ?? 'N/A' }}</strong>
                                            </td>
                                            <td class="text-end">
                                                ${{ number_format($approval->request_amount ?? 0, 2) }}
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    Level {{ $approval->current_level ?? 1 }} / {{ $approval->required_levels ?? 1 }}
                                                </span>
                                            </td>
                                            <td>{{ $approval->submitted_at ? $approval->submitted_at->format('m/d/Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $isOverdue ? 'danger' : 'warning' }}">
                                                    {{ $age }}h
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.approvals.show', $approval->request_id) }}"
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $pendingApprovals->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
