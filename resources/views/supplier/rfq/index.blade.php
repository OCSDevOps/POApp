@extends('supplier.layouts.app')

@section('title', 'RFQs')

@section('content')
<h4 class="mb-3">Assigned RFQs</h4>
<div class="card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>RFQ #</th>
                    <th>Project</th>
                    <th>Due</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($rfqs as $row)
                    <tr>
                        <td>{{ $row->rfq->rfq_no ?? '' }}</td>
                        <td>{{ $row->rfq->project->proj_name ?? '—' }}</td>
                        <td>{{ optional($row->rfq->rfq_due_date)->format('Y-m-d') }}</td>
                        <td><span class="badge {{ $row->rfq->rfq_status == 4 ? 'bg-success' : 'bg-secondary' }}">{{ $row->rfq->status_text ?? 'Open' }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('supplier.rfq.show', $row->rfq->rfq_id) }}" class="btn btn-sm btn-primary">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No RFQs assigned yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body pt-2">
        {{ $rfqs->links() }}
    </div>
</div>
@endsection
