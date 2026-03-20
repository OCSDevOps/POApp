@extends('layouts.admin')

@section('title', $company->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-building"></i> {{ $company->name }}
            @if($company->status == 1)
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-secondary">Inactive</span>
            @endif
        </h1>
        <div>
            @if(session('company_id') != $company->id)
                <form action="{{ route('admin.tenants.switch', $company->id) }}" method="GET" class="d-inline">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-exchange-alt"></i> Switch To
                    </button>
                </form>
            @else
                <span class="badge bg-success p-2"><i class="fas fa-check"></i> Current Company</span>
            @endif
            <a href="{{ route('admin.tenants.edit', $company->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $company->users_count }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Projects</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $company->projects_count }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-project-diagram fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Purchase Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $company->purchase_orders_count }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-file-invoice fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Suppliers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $company->suppliers_count }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-truck fa-2x text-gray-300"></i></div>
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
                        <tr><th width="30%">ID:</th><td>{{ $company->id }}</td></tr>
                        <tr><th>Name:</th><td>{{ $company->name }}</td></tr>
                        <tr><th>Subdomain:</th><td>{{ $company->subdomain ?? 'N/A' }}</td></tr>
                        <tr><th>Email:</th><td>{{ $company->email ?? 'N/A' }}</td></tr>
                        <tr><th>Phone:</th><td>{{ $company->phone ?? 'N/A' }}</td></tr>
                        <tr><th>Address:</th><td>{{ $company->address ?? 'N/A' }}</td></tr>
                        <tr><th>City:</th><td>{{ $company->city ?? 'N/A' }}</td></tr>
                        <tr><th>State:</th><td>{{ $company->state ?? 'N/A' }}</td></tr>
                        <tr><th>ZIP:</th><td>{{ $company->zip ?? 'N/A' }}</td></tr>
                        <tr><th>Country:</th><td>{{ $company->country ?? 'N/A' }}</td></tr>
                        <tr><th>Created:</th><td>{{ $company->created_at ? $company->created_at->format('M d, Y h:i A') : 'N/A' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Users -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Users ({{ $company->users_count }})</h6>
                </div>
                <div class="card-body">
                    @if($company->users && $company->users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($company->users as $user)
                                        <tr>
                                            <td>{{ $user->name ?? $user->u_name }}</td>
                                            <td>{{ $user->email ?? $user->u_email }}</td>
                                            <td>
                                                @if($user->u_type == 1)
                                                    <span class="badge bg-primary">Admin</span>
                                                @else
                                                    <span class="badge bg-secondary">User</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($user->u_status == 1)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No users found.</p>
                    @endif
                </div>
            </div>

            <!-- Recent Projects -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Projects</h6>
                </div>
                <div class="card-body">
                    @if($company->projects && $company->projects->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($company->projects as $project)
                                        <tr>
                                            <td>{{ $project->proj_name }}</td>
                                            <td>{{ $project->proj_number }}</td>
                                            <td>
                                                @if($project->proj_status == 1)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">No projects found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
