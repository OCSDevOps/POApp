@extends('supplier.layouts.app')

@section('title', 'Pricing')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Item Pricing</h4>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('supplier.pricing.import') }}">Import CSV</a>
        <a class="btn btn-primary btn-sm" href="{{ route('supplier.pricing.create') }}">Add Price</a>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
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
            <td>{{ $row->item->item_name ?? 'Item #'.$row->item_id }}</td>
            <td>{{ $row->project->proj_name ?? 'All Projects' }}</td>
            <td>${{ number_format($row->unit_price, 2) }}</td>
            <td>{{ $row->effective_from->format('Y-m-d') }} @if($row->effective_to) – {{ $row->effective_to->format('Y-m-d') }} @endif</td>
            <td>
              <span class="badge {{ $row->status ? 'bg-success' : 'bg-secondary' }}">{{ $row->status ? 'Active' : 'Expired' }}</span>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center py-4 text-muted">No pricing added yet.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-body pt-2">
      {{ $pricing->links() }}
    </div>
  </div>
@endsection
