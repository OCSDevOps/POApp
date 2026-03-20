@extends('layouts.admin')

@section('title', 'Contract Change Orders')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-exchange-alt"></i> Contract Change Orders</h4>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Contract</label>
                                <select name="contract_id" class="form-select">
                                    <option value="">All Contracts</option>
                                    @foreach($contracts as $contract)
                                        <option value="{{ $contract->contract_id }}" {{ request('contract_id') == $contract->contract_id ? 'selected' : '' }}>
                                            {{ $contract->contract_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                            @if(request()->hasAny(['contract_id', 'status']))
                                <div class="col-md-2 d-flex align-items-end">
                                    <a href="{{ route('admin.contract-change-orders.index') }}" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($changeOrders->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No contract change orders found.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover datatable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>CCO #</th>
                                        <th>Contract #</th>
                                        <th>Project</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($changeOrders as $index => $cco)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $cco->cco_number }}</strong></td>
                                            <td>
                                                {{ $cco->contract->contract_number ?? 'N/A' }}
                                            </td>
                                            <td>{{ $cco->contract->project->proj_name ?? 'N/A' }}</td>
                                            <td class="text-end">
                                                <strong class="{{ $cco->cco_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $cco->cco_amount >= 0 ? '+' : '' }}${{ number_format(abs($cco->cco_amount), 2) }}
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="badge
                                                    @if($cco->cco_status == 'draft') bg-secondary
                                                    @elseif($cco->cco_status == 'pending_approval') bg-warning
                                                    @elseif($cco->cco_status == 'approved') bg-success
                                                    @elseif($cco->cco_status == 'rejected') bg-danger
                                                    @elseif($cco->cco_status == 'cancelled') bg-dark
                                                    @endif">
                                                    {{ str_replace('_', ' ', ucfirst($cco->cco_status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $cco->created_at->format('m/d/Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.contract-change-orders.show', $cco->cco_id) }}"
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
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
@endsection
