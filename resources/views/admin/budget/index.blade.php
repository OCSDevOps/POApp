@extends('layouts.admin')

@section('title', 'Budgets')

@section('content')
<div class="container-fluid">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-wallet me-1"></i> Budgets
            </h6>
            <div>
                <a href="{{ route('admin.budget.summary') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-chart-pie me-1"></i> Budget Summary
                </a>
                <a href="{{ route('admin.budget.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Create Budget
                </a>
            </div>
        </div>
        <div class="card-body">

            {{-- Filters --}}
            <form method="GET" action="{{ route('admin.budget.index') }}" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-select">
                            <option value="">-- All Projects --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->proj_id }}" @selected(request('project_id') == $project->proj_id)>
                                    {{ $project->proj_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cost Code</label>
                        <select name="cost_code_id" class="form-select">
                            <option value="">-- All Cost Codes --</option>
                            @foreach($costCodes as $costCode)
                                <option value="{{ $costCode->cc_id }}" @selected(request('cost_code_id') == $costCode->cc_id)>
                                    {{ $costCode->cc_no }} - {{ $costCode->cc_description }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Fiscal Year</label>
                        <select name="fiscal_year" class="form-select">
                            <option value="">-- All Years --</option>
                            @for($y = date('Y') + 1; $y >= date('Y') - 5; $y--)
                                <option value="{{ $y }}" @selected(request('fiscal_year') == $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.budget.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-times me-1"></i> Clear
                        </a>
                    </div>
                </div>
            </form>

            {{-- Data Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="budgetsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Cost Code</th>
                            <th>Fiscal Year</th>
                            <th class="text-end">Original</th>
                            <th class="text-end">Revised</th>
                            <th class="text-end">Committed</th>
                            <th class="text-end">Spent</th>
                            <th class="text-end">Remaining</th>
                            <th class="text-center">Utilization %</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($budgets as $budget)
                            @php
                                $utilization = $budget->utilization_percent;
                            @endphp
                            <tr>
                                <td>{{ $budget->project->proj_name ?? '—' }}</td>
                                <td>{{ ($budget->costCode->cc_no ?? '') . ' - ' . ($budget->costCode->cc_description ?? '—') }}</td>
                                <td>{{ $budget->budget_fiscal_year }}</td>
                                <td class="text-end">${{ number_format($budget->budget_original_amount, 2) }}</td>
                                <td class="text-end">${{ number_format($budget->budget_revised_amount, 2) }}</td>
                                <td class="text-end">${{ number_format($budget->budget_committed_amount, 2) }}</td>
                                <td class="text-end">${{ number_format($budget->budget_spent_amount, 2) }}</td>
                                <td class="text-end">${{ number_format($budget->remaining_amount, 2) }}</td>
                                <td class="text-center">
                                    @if($utilization > 90)
                                        <span class="badge bg-danger">{{ number_format($utilization, 1) }}%</span>
                                    @elseif($utilization >= 75)
                                        <span class="badge bg-warning text-dark">{{ number_format($utilization, 1) }}%</span>
                                    @else
                                        <span class="badge bg-success">{{ number_format($utilization, 1) }}%</span>
                                    @endif
                                </td>
                                <td>
                                    @if($budget->budget_status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.budget.show', $budget->budget_id) }}" class="btn btn-sm btn-outline-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.budget.edit', $budget->budget_id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" title="Delete"
                                            data-url="{{ route('admin.budget.destroy', $budget->budget_id) }}" data-name="{{ ($budget->project->proj_name ?? '') . ' / ' . ($budget->costCode->cc_no ?? '') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">No budgets found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $budgets->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</div>
@include('partials.delete-modal')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#budgetsTable').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "order": [[0, 'asc'], [1, 'asc']],
        "columnDefs": [
            { "orderable": false, "targets": 10 }
        ]
    });
});
</script>
@endpush
