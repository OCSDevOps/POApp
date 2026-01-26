@extends('layouts.admin')

@section('title','Performed Checklists')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Performed Checklists</h4>
        <p class="text-muted mb-0">Completed runs of checklist templates.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('admin.performchecklists.create') }}"><i class="fa fa-plus me-1"></i> Record Checklist</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Checklist</th>
                    <th>Equipment</th>
                    <th>Date</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($performances as $performance)
                    <tr>
                        <td>{{ $performance->cl_p_id }}</td>
                        <td>{{ $performance->checklist->cl_name ?? '' }}</td>
                        <td>{{ $performance->equipment->eqm_asset_name ?? 'N/A' }}</td>
                        <td>{{ $performance->cl_p_date }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.performchecklists.show', $performance) }}"><i class="fa fa-eye"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No checklist runs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-2">{{ $performances->links() }}</div>
    </div>
</div>
@endsection
