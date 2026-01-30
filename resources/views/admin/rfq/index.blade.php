@extends('admin.layouts.app')

@section('title', 'RFQs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">RFQs</h4>
    <a class="btn btn-primary btn-sm" href="{{ route('admin.rfq.create') }}">Create RFQ</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>RFQ #</th>
                        <th>Title</th>
                        <th>Project</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rfqs as $rfq)
                        <tr>
                            <td>{{ $rfq->rfq_no }}</td>
                            <td>{{ $rfq->rfq_title }}</td>
                            <td>{{ $rfq->project->proj_name ?? '—' }}</td>
                            <td>{{ optional($rfq->rfq_due_date)->format('Y-m-d') }}</td>
                            <td>{{ $rfq->status_text }}</td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.rfq.show', $rfq->rfq_id) }}">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No RFQs yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-body pt-2">
        {{ $rfqs->links() }}
    </div>
</div>
@endsection
