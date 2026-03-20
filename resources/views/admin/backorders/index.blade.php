@extends('layouts.admin')

@section('title', 'Backorders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Backorders</h4>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>PO #</th>
                    <th>Item</th>
                    <th>Ordered</th>
                    <th>Backordered</th>
                    <th>Expected</th>
                    <th>Project</th>
                    <th>Supplier</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backorders as $row)
                    <tr>
                        <td>{{ $row->porder_no }}</td>
                        <td>{{ $row->po_detail_item }}</td>
                        <td>{{ $row->po_detail_quantity }}</td>
                        <td class="fw-semibold text-danger">{{ $row->backordered_qty }}</td>
                        <td>{{ $row->expected_backorder_date ?? '—' }}</td>
                        <td>{{ $row->proj_name ?? '—' }}</td>
                        <td>{{ $row->sup_name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No backorders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
