@extends('layouts.admin')

@section('title', 'PO Change Orders')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">PO Change Orders</h4>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select name="project_id" class="form-select">
                                    <option value="">All Projects</option>
                                    @foreach($projects as $proj)
                                        <option value="{{ $proj->proj_id }}" {{ request('project_id') == $proj->proj_id ? 'selected' : '' }}>
                                            {{ $proj->proj_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="pending_approval" {{ request('status') == 'pending_approval' ? 'selected' : '' }}>Pending Approval</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($changeOrders->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No PO change orders found.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>PCO Number</th>
                                        <th>PO Number</th>
                                        <th>Project</th>
                                        <th>Supplier</th>
                                        <th>Type</th>
                                        <th>Previous Total</th>
                                        <th>New Total</th>
                                        <th>Change</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($changeOrders as $pco)
                                        <tr>
                                            <td><strong>{{ $pco->poco_number }}</strong></td>
                                            <td>
                                                <a href="{{ route('admin.porder.show', $pco->purchaseOrder->porder_id) }}">
                                                    {{ $pco->purchaseOrder->porder_no }}
                                                </a>
                                            </td>
                                            <td>{{ $pco->purchaseOrder->project->proj_name }}</td>
                                            <td>{{ $pco->purchaseOrder->supplier->sup_name }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ str_replace('_', ' ', ucwords($pco->poco_type)) }}
                                                </span>
                                            </td>
                                            <td class="text-end">${{ number_format($pco->poco_previous_total, 2) }}</td>
                                            <td class="text-end">${{ number_format($pco->poco_new_total, 2) }}</td>
                                            <td class="text-end">
                                                <strong class="{{ $pco->poco_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $pco->poco_amount >= 0 ? '+' : '' }}${{ number_format(abs($pco->poco_amount), 2) }}
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if($pco->poco_status == 'approved') bg-success
                                                    @elseif($pco->poco_status == 'rejected') bg-danger
                                                    @elseif($pco->poco_status == 'pending_approval') bg-warning
                                                    @elseif($pco->poco_status == 'cancelled') bg-secondary
                                                    @else bg-info
                                                    @endif">
                                                    {{ str_replace('_', ' ', ucfirst($pco->poco_status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $pco->created_at->format('m/d/Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.po-change-orders.show', $pco->poco_id) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $changeOrders->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
