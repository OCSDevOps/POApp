@extends('layouts.admin')

@section('title', 'Tenant Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-building"></i> Company (Tenant) Management
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Company
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Company Name</th>
                                    <th>Subdomain</th>
                                    <th class="text-center">Users</th>
                                    <th class="text-center">Projects</th>
                                    <th class="text-center">POs</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($companies as $company)
                                <tr>
                                    <td>{{ $company->id }}</td>
                                    <td>
                                        <strong>{{ $company->name }}</strong>
                                        @if($company->id == session('company_id'))
                                            <span class="badge badge-info">Current</span>
                                        @endif
                                    </td>
                                    <td>{{ $company->subdomain ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="badge badge-info">{{ $company->users_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $company->projects_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-warning">{{ $company->purchase_orders_count }}</span>
                                    </td>
                                    <td>
                                        @if($company->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.tenants.show', $company->id) }}" 
                                               class="btn btn-sm btn-info" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.tenants.edit', $company->id) }}" 
                                               class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($company->id != session('company_id'))
                                                <a href="{{ route('admin.tenants.switch', $company->id) }}" 
                                                   class="btn btn-sm btn-warning" title="Switch to this company"
                                                   onclick="return confirm('Switch to {{ $company->name }}?')">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </a>
                                                <form action="{{ route('admin.tenants.destroy', $company->id) }}" 
                                                      method="POST" style="display: inline;"
                                                      onsubmit="return confirm('Disable this company?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Disable">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle"></i> No companies found.
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $companies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $companies->total() }}</h3>
                    <p>Total Companies</p>
                </div>
                <div class="icon">
                    <i class="fas fa-building"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $companies->where('status', 1)->count() }}</h3>
                    <p>Active Companies</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $companies->sum('users_count') }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $companies->sum('projects_count') }}</h3>
                    <p>Total Projects</p>
                </div>
                <div class="icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
