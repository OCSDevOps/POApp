@extends('layouts.admin')

@section('title', 'Budget Change Orders - ' . $project->proj_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Budget Change Orders: {{ $project->proj_name }}</h4>
                    <div>
                        <a href="{{ route('admin.budget-change-orders.create', $project->proj_id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create Budget Change Order
                        </a>
                        <a href="{{ route('admin.budgets.view', $project->proj_id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Budget
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($changeOrders->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No budget change orders for this project yet.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>BCO Number</th>
                                        <th>Cost Code</th>
                                        <th>Type</th>
                                        <th>Previous Budget</th>
                                        <th>New Budget</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Created By</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($changeOrders as $bco)
                                        <tr>
                                            <td><strong>{{ $bco->bco_number }}</strong></td>
                                            <td>
                                                {{ $bco->costCode->getFormattedCode() }}<br>
                                                <small class="text-muted">{{ Str::limit($bco->costCode->cc_description, 30) }}</small>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if($bco->bco_type == 'increase') bg-success
                                                    @elseif($bco->bco_type == 'decrease') bg-warning
                                                    @else bg-info
                                                    @endif">
                                                    {{ ucfirst($bco->bco_type) }}
                                                </span>
                                            </td>
                                            <td class="text-end">${{ number_format($bco->bco_previous_budget, 2) }}</td>
                                            <td class="text-end">${{ number_format($bco->bco_new_budget, 2) }}</td>
                                            <td class="text-end">
                                                <strong class="{{ $bco->bco_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $bco->bco_amount >= 0 ? '+' : '' }}${{ number_format(abs($bco->bco_amount), 2) }}
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if($bco->bco_status == 'approved') bg-success
                                                    @elseif($bco->bco_status == 'rejected') bg-danger
                                                    @elseif($bco->bco_status == 'pending_approval') bg-warning
                                                    @elseif($bco->bco_status == 'cancelled') bg-secondary
                                                    @else bg-info
                                                    @endif">
                                                    {{ str_replace('_', ' ', ucfirst($bco->bco_status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $bco->creator->user_name ?? 'Unknown' }}</td>
                                            <td>{{ $bco->created_at->format('m/d/Y') }}</td>
                                            <td>
                                                <a href="{{ route('admin.budget-change-orders.show', ['projectId' => $project->proj_id, 'id' => $bco->bco_id]) }}" 
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
                            {{ $changeOrders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
