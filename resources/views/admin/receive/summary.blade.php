@extends('layouts.admin')

@section('title', 'Receiving Summary')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Receiving Summary</h4>
        <a href="{{ route('admin.receive.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Receive Orders
        </a>
    </div>

    {{-- Summary Statistics --}}
    @if(isset($summary) && count($summary) > 0)
    @php
        $totalOrdered = $summary->sum('total_ordered_qty');
        $totalReceived = $summary->sum('total_received_qty');
        $totalPending = $summary->sum('total_pending_qty');
        $totalValue = $summary->sum('total_received_value');
    @endphp
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-primary border-4">
                <div class="card-body py-2">
                    <div class="text-muted small">Total Ordered</div>
                    <div class="fs-4 fw-bold">{{ number_format($totalOrdered) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-success border-4">
                <div class="card-body py-2">
                    <div class="text-muted small">Total Received</div>
                    <div class="fs-4 fw-bold text-success">{{ number_format($totalReceived) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-warning border-4">
                <div class="card-body py-2">
                    <div class="text-muted small">Total Pending</div>
                    <div class="fs-4 fw-bold text-warning">{{ number_format($totalPending) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-info border-4">
                <div class="card-body py-2">
                    <div class="text-muted small">Total Received Value</div>
                    <div class="fs-4 fw-bold">${{ number_format($totalValue, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Receiving Summary Report</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="summaryTable">
                    <thead class="table-light">
                        <tr>
                            <th>PO #</th>
                            <th>Project</th>
                            <th>Supplier</th>
                            <th class="text-center">Ordered Qty</th>
                            <th class="text-center">Received Qty</th>
                            <th class="text-center">Pending Qty</th>
                            <th class="text-end">Received Value</th>
                            <th class="text-center">Completion</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($summary as $row)
                            @php
                                $ordered = $row->total_ordered_qty ?? 0;
                                $received = $row->total_received_qty ?? 0;
                                $pending = $row->total_pending_qty ?? ($ordered - $received);
                                $pct = $ordered > 0 ? round(($received / $ordered) * 100) : 0;
                            @endphp
                            <tr>
                                <td><strong>{{ $row->porder_no ?? '—' }}</strong></td>
                                <td>{{ $row->proj_name ?? '—' }}</td>
                                <td>{{ $row->sup_name ?? '—' }}</td>
                                <td class="text-center">{{ $ordered }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $received }}</span>
                                </td>
                                <td class="text-center">
                                    @if($pending > 0)
                                        <span class="badge bg-warning">{{ $pending }}</span>
                                    @else
                                        <span class="badge bg-success">0</span>
                                    @endif
                                </td>
                                <td class="text-end">${{ number_format($row->total_received_value ?? 0, 2) }}</td>
                                <td class="text-center" style="min-width: 120px;">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar
                                            @if($pct >= 100) bg-success
                                            @elseif($pct >= 50) bg-info
                                            @elseif($pct > 0) bg-warning
                                            @else bg-secondary
                                            @endif"
                                             role="progressbar" style="width: {{ $pct }}%"
                                             aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ $pct }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($pct >= 100)
                                        <span class="badge bg-success">Fully Received</span>
                                    @elseif($pct > 0)
                                        <span class="badge bg-warning">Partial</span>
                                    @else
                                        <span class="badge bg-secondary">Not Received</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No receiving summary data available.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#summaryTable').DataTable({
        paging: true,
        pageLength: 25,
        searching: true,
        order: [[7, 'asc']],
        columnDefs: [
            { orderable: false, targets: [7] }
        ]
    });
});
</script>
@endpush
@endsection
