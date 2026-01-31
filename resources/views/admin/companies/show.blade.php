@extends('layouts.admin')

@section('title', $company->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-building"></i> {{ $company->name }}
            @if($company->status == 1)
                <span class="badge badge-success">Active</span>
            @else
                <span class="badge badge-secondary">Inactive</span>
            @endif
        </h1>
        <div>
            @if(session('company_id') != $company->id)
                <form action="{{ route('admin.companies.switch', $company) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-exchange-alt"></i> Switch to This Company
                    </button>
                </form>
            @else
                <span class="badge badge-success p-2"><i class="fas fa-check"></i> Current Company</span>
            @endif
            <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Projects</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['active_projects'] }} / {{ $stats['total_projects'] }}
                                <small class="text-muted">active</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-project-diagram fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Purchase Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['pending_pos'] }} / {{ $stats['total_pos'] }}
                                <small class="text-muted">pending</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Suppliers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_suppliers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Company Details -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Company Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="30%">ID:</th>
                                <td>{{ $company->id }}</td>
                            </tr>
                            <tr>
                                <th>Name:</th>
                                <td>{{ $company->name }}</td>
                            </tr>
                            <tr>
                                <th>Subdomain:</th>
                                <td>{{ $company->subdomain ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($company->status == 1)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created:</th>
                                <td>{{ $company->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $company->updated_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Items Count:</th>
                                <td>{{ $stats['total_items'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Purchase Orders</h6>
                </div>
                <div class="card-body">
                    @if($company->purchaseOrders && $company->purchaseOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>PO #</th>
                                        <th>Project</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($company->purchaseOrders->take(5) as $po)
                                        <tr>
                                            <td>{{ $po->porder_no }}</td>
                                            <td>{{ $po->project->proj_name ?? '-' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $po->porder_general_status == 'pending' ? 'warning' : 'success' }}">
                                                    {{ ucfirst($po->porder_general_status) }}
                                                </span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($po->porder_date)->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No purchase orders yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    @if($stats['total_users'] == 0 && $stats['total_projects'] == 0 && $stats['total_pos'] == 0)
        <div class="card border-danger shadow mb-4">
            <div class="card-header bg-danger text-white py-3">
                <h6 class="m-0 font-weight-bold">Danger Zone</h6>
            </div>
            <div class="card-body">
                <p>Once you delete a company, there is no going back. Please be certain.</p>
                <form action="{{ route('admin.companies.destroy', $company) }}" 
                      method="POST" 
                      onsubmit="return confirm('Are you absolutely sure you want to delete {{ $company->name }}? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete This Company
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
