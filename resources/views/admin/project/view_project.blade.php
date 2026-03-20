@extends('layouts.admin')

@section('title', $project->proj_name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-project-diagram"></i> {{ $project->proj_name }}
            @if($project->proj_status == 1)
                <span class="badge bg-success">Active</span>
            @else
                <span class="badge bg-secondary">Inactive</span>
            @endif
        </h1>
        <div>
            <a href="{{ route('admin.projects.edit', $project->proj_id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
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

    <div class="row">
        <!-- Project Details -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Project Details</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="30%">ID:</th>
                                <td>{{ $project->proj_id }}</td>
                            </tr>
                            <tr>
                                <th>Project Number:</th>
                                <td>{{ $project->proj_number }}</td>
                            </tr>
                            <tr>
                                <th>Project Name:</th>
                                <td>{{ $project->proj_name }}</td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td>{{ $project->proj_address }}</td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td>{{ $project->proj_description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Contact:</th>
                                <td>{{ $project->proj_contact }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($project->proj_status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created By:</th>
                                <td>{{ $project->proj_createby ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $project->proj_createdate ? \Carbon\Carbon::parse($project->proj_createdate)->format('M d, Y h:i A') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Modified By:</th>
                                <td>{{ $project->proj_modifyby ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Modified At:</th>
                                <td>{{ $project->proj_modifydate ? \Carbon\Carbon::parse($project->proj_modifydate)->format('M d, Y h:i A') : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Purchase Orders -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Purchase Orders</h6>
                </div>
                <div class="card-body">
                    @if($project->purchaseOrders && $project->purchaseOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>PO #</th>
                                        <th>Supplier</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->purchaseOrders->take(10) as $po)
                                        <tr>
                                            <td>{{ $po->porder_no }}</td>
                                            <td>{{ $po->supplier->sup_name ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $po->porder_status == 1 ? 'success' : 'secondary' }}">
                                                    {{ $po->porder_status == 1 ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>{{ $po->porder_createdate ? \Carbon\Carbon::parse($po->porder_createdate)->format('M d, Y') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No purchase orders found for this project.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // View page scripts if needed
</script>
@endpush
