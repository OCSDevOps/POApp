@extends('layouts.admin')

@section('title', 'Budget Drill-down - ' . ($costCode->cc_name ?? 'Unknown'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-search-dollar"></i> 
                        Budget Drill-down: {{ $costCode->cc_no ?? 'N/A' }} - {{ $costCode->cc_name ?? 'Unknown' }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Report
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($costCode)
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Cost Code:</strong> {{ $costCode->cc_no }}
                        </div>
                        <div class="col-md-3">
                            <strong>Description:</strong> {{ $costCode->cc_name }}
                        </div>
                        <div class="col-md-3">
                            <strong>Level:</strong> {{ $costCode->level ?? 'N/A' }}
                        </div>
                        <div class="col-md-3">
                            <strong>Parent:</strong> {{ $costCode->parent_code ?? 'None' }}
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Cost code information not found.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Orders Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice-dollar"></i> 
                        Purchase Orders (Committed)
                        <span class="badge badge-light ml-2">{{ count($purchaseOrders ?? []) }} POs</span>
                    </h3>
                </div>
                <div class="card-body">
                    @if($purchaseOrders && count($purchaseOrders) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>PO #</th>
                                        <th>Date</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-center">Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrders as $po)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.porder.show', $po->porder_id) }}" target="_blank">
                                                {{ $po->porder_no }}
                                            </a>
                                        </td>
                                        <td>{{ $po->porder_date ? date('m/d/Y', strtotime($po->porder_date)) : 'N/A' }}</td>
                                        <td class="text-right">${{ number_format($po->po_amount ?? 0, 2) }}</td>
                                        <td class="text-center">
                                            @if($po->porder_status == 'approved' || $po->porder_status == 2)
                                                <span class="badge badge-success">Approved</span>
                                            @elseif($po->porder_status == 'pending' || $po->porder_status == 1)
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($po->porder_status == 'rejected' || $po->porder_status == 3)
                                                <span class="badge badge-danger">Rejected</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $po->porder_status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.porder.show', $po->porder_id) }}" class="btn btn-sm btn-info" target="_blank">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('admin.porder.pdf', $po->porder_id) }}" class="btn btn-sm btn-danger" target="_blank">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light font-weight-bold">
                                        <td colspan="2" class="text-right">Total Committed:</td>
                                        <td class="text-right">${{ number_format($purchaseOrders->sum('po_amount'), 2) }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No purchase orders found for this cost code.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Receive Orders Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">
                        <i class="fas fa-truck-loading"></i> 
                        Receive Orders (Actual)
                        <span class="badge badge-light ml-2">{{ count($receiveOrders ?? []) }} ROs</span>
                    </h3>
                </div>
                <div class="card-body">
                    @if($receiveOrders && count($receiveOrders) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>RO #</th>
                                        <th>Date</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receiveOrders as $ro)
                                    <tr>
                                        <td>{{ $ro->ro_number }}</td>
                                        <td>{{ $ro->ro_date ? date('m/d/Y', strtotime($ro->ro_date)) : 'N/A' }}</td>
                                        <td class="text-right">${{ number_format($ro->ro_amount ?? 0, 2) }}</td>
                                        <td class="text-center">
                                            @if($ro->ro_status == 'received' || $ro->ro_status == 2)
                                                <span class="badge badge-success">Received</span>
                                            @elseif($ro->ro_status == 'partial' || $ro->ro_status == 1)
                                                <span class="badge badge-warning">Partial</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $ro->ro_status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light font-weight-bold">
                                        <td colspan="2" class="text-right">Total Actual:</td>
                                        <td class="text-right">${{ number_format($receiveOrders->sum('ro_amount'), 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No receive orders found for this cost code.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">
                        <i class="fas fa-calculator"></i> Summary
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Committed (POs)</span>
                                    <span class="info-box-number">${{ number_format($purchaseOrders->sum('po_amount') ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Actual (Received)</span>
                                    <span class="info-box-number">${{ number_format($receiveOrders->sum('ro_amount') ?? 0, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-chart-pie"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Spent</span>
                                    <span class="info-box-number">${{ number_format(($purchaseOrders->sum('po_amount') ?? 0) + ($receiveOrders->sum('ro_amount') ?? 0), 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
