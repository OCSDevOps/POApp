@extends('layouts.admin')

@section('title', 'Contracts')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 font-weight-bold text-primary">
            <i class="fas fa-file-contract me-1"></i> Contracts
        </h5>
        <a href="{{ route('admin.contracts.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i> New Contract
        </a>
    </div>

    {{-- Filters --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter me-1"></i> Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.contracts.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-select">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->proj_id }}" {{ request('project_id') == $project->proj_id ? 'selected' : '' }}>
                                    {{ $project->proj_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Subcontractor</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">All Subcontractors</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->sup_id }}" {{ request('supplier_id') == $supplier->sup_id ? 'selected' : '' }}>
                                    {{ $supplier->sup_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Contracts Table --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            @if($contracts->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-1"></i> No contracts found. Click "New Contract" to create one.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover datatable" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Contract #</th>
                                <th>Project</th>
                                <th>Subcontractor</th>
                                <th class="text-end">Original Value</th>
                                <th class="text-end">Revised Value</th>
                                <th>Status</th>
                                <th>Start Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contracts as $index => $contract)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('admin.contracts.show', $contract->id) }}">
                                            <strong>{{ $contract->contract_number }}</strong>
                                        </a>
                                    </td>
                                    <td>{{ $contract->project->proj_name ?? '—' }}</td>
                                    <td>{{ $contract->supplier->sup_name ?? '—' }}</td>
                                    <td class="text-end">${{ number_format($contract->original_value, 2) }}</td>
                                    <td class="text-end">${{ number_format($contract->revised_value, 2) }}</td>
                                    <td>
                                        @switch($contract->status)
                                            @case('draft')
                                                <span class="badge bg-secondary">Draft</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                                @break
                                            @case('approved')
                                                <span class="badge bg-info">Approved</span>
                                                @break
                                            @case('active')
                                                <span class="badge bg-success">Active</span>
                                                @break
                                            @case('completed')
                                                <span class="badge bg-primary">Completed</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                                @break
                                            @case('closed')
                                                <span class="badge bg-dark">Closed</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($contract->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('m/d/Y') : '—' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.contracts.show', $contract->id) }}" class="btn btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.contracts.edit', $contract->id) }}" class="btn btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.contracts.destroy', $contract->id) }}"
                                                  class="d-inline" onsubmit="return confirm('Are you sure you want to delete this contract?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
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
@endsection
