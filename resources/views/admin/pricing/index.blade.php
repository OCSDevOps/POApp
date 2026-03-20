@extends('layouts.admin')

@section('title', 'Supplier Pricing')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Supplier Pricing</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Item</th>
                        <th>Project</th>
                        <th>Unit Price</th>
                        <th>Effective</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pricing as $row)
                        <tr>
                            <td>{{ $row->supplier->sup_name ?? 'Supplier #'.$row->supplier_id }}</td>
                            <td>{{ $row->item->item_name ?? 'Item #'.$row->item_id }}</td>
                            <td>{{ $row->project->proj_name ?? 'All Projects' }}</td>
                            <td>${{ number_format($row->unit_price, 2) }}</td>
                            <td>{{ $row->effective_from->format('Y-m-d') }} @if($row->effective_to) – {{ $row->effective_to->format('Y-m-d') }} @endif</td>
                            <td><span class="badge {{ $row->status ? 'bg-success' : 'bg-secondary' }}">{{ $row->status ? 'Active' : 'Expired' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No pricing records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-body pt-2">
        {{ $pricing->links() }}
    </div>
</div>
@endsection
