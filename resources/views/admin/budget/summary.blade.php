@extends('layouts.admin')

@section('title', 'Budget Summary')

@section('content')
<div class="container-fluid">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-chart-pie me-1"></i> Budget Summary
            </h6>
            <a href="{{ route('admin.budget.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Budgets
            </a>
        </div>
        <div class="card-body">

            {{-- Project Filter --}}
            <form method="GET" action="{{ route('admin.budget.summary') }}" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Filter by Project</label>
                        <select name="project_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- All Projects --</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->proj_id }}" @selected(request('project_id') == $project->proj_id)>
                                    {{ $project->proj_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            {{-- Summary Cards --}}
            @php
                $totalBudget = $summary->sum('budget_revised_amount') ?: $summary->sum('budget_original_amount');
                $totalCommitted = $summary->sum('budget_committed_amount');
                $totalSpent = $summary->sum('budget_spent_amount');
                $totalAvailable = $totalBudget - $totalCommitted - $totalSpent;
            @endphp

            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Budget</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($totalBudget, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Committed</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($totalCommitted, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Spent</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($totalSpent, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Available</div>
                                    <div class="h5 mb-0 font-weight-bold {{ $totalAvailable < 0 ? 'text-danger' : 'text-gray-800' }}">
                                        ${{ number_format($totalAvailable, 2) }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="summaryTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Project</th>
                            <th>Cost Code</th>
                            <th class="text-end">Original</th>
                            <th class="text-end">Revised</th>
                            <th class="text-end">Committed</th>
                            <th class="text-end">Spent</th>
                            <th class="text-end">Available</th>
                            <th class="text-center">Utilization %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($summary as $row)
                            @php
                                $revised = $row->budget_revised_amount ?: $row->budget_original_amount;
                                $available = $revised - ($row->budget_committed_amount + $row->budget_spent_amount);
                                $utilization = $revised > 0 ? (($row->budget_committed_amount + $row->budget_spent_amount) / $revised) * 100 : 0;
                            @endphp
                            <tr>
                                <td>{{ $row->project->proj_name ?? ($row->proj_name ?? '—') }}</td>
                                <td>{{ ($row->costCode->cc_no ?? ($row->cc_no ?? '')) . ' - ' . ($row->costCode->cc_description ?? ($row->cc_description ?? '—')) }}</td>
                                <td class="text-end">${{ number_format($row->budget_original_amount, 2) }}</td>
                                <td class="text-end">${{ number_format($revised, 2) }}</td>
                                <td class="text-end">${{ number_format($row->budget_committed_amount, 2) }}</td>
                                <td class="text-end">${{ number_format($row->budget_spent_amount, 2) }}</td>
                                <td class="text-end">
                                    <strong class="{{ $available < 0 ? 'text-danger' : 'text-success' }}">
                                        ${{ number_format($available, 2) }}
                                    </strong>
                                </td>
                                <td class="text-center">
                                    @if($utilization > 90)
                                        <span class="badge bg-danger">{{ number_format($utilization, 1) }}%</span>
                                    @elseif($utilization >= 75)
                                        <span class="badge bg-warning text-dark">{{ number_format($utilization, 1) }}%</span>
                                    @else
                                        <span class="badge bg-success">{{ number_format($utilization, 1) }}%</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No budget data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="2">Totals</td>
                            <td class="text-end">${{ number_format($summary->sum('budget_original_amount'), 2) }}</td>
                            <td class="text-end">${{ number_format($totalBudget, 2) }}</td>
                            <td class="text-end">${{ number_format($totalCommitted, 2) }}</td>
                            <td class="text-end">${{ number_format($totalSpent, 2) }}</td>
                            <td class="text-end">
                                <strong class="{{ $totalAvailable < 0 ? 'text-danger' : 'text-success' }}">
                                    ${{ number_format($totalAvailable, 2) }}
                                </strong>
                            </td>
                            <td class="text-center">
                                @php
                                    $overallUtil = $totalBudget > 0 ? (($totalCommitted + $totalSpent) / $totalBudget) * 100 : 0;
                                @endphp
                                @if($overallUtil > 90)
                                    <span class="badge bg-danger">{{ number_format($overallUtil, 1) }}%</span>
                                @elseif($overallUtil >= 75)
                                    <span class="badge bg-warning text-dark">{{ number_format($overallUtil, 1) }}%</span>
                                @else
                                    <span class="badge bg-success">{{ number_format($overallUtil, 1) }}%</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#summaryTable').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "order": [[0, 'asc'], [1, 'asc']]
    });
});
</script>
@endpush
