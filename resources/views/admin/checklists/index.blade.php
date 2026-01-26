@extends('layouts.admin')

@section('title','Checklists')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">Checklists</h4>
        <p class="text-muted mb-0">Define recurring checklist templates.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('admin.checklists.create') }}"><i class="fa fa-plus me-1"></i> New Checklist</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Frequency</th>
                    <th>Start</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($checklists as $checklist)
                    <tr>
                        <td>{{ $checklist->cl_name }}</td>
                        <td>{{ $checklist->cl_frequency }}</td>
                        <td>{{ $checklist->cl_start_date }}</td>
                        <td>{{ $checklist->items_count }}</td>
                        <td>{!! $checklist->status ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' !!}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.checklists.edit', $checklist) }}"><i class="fa fa-pen"></i></a>
                            <form method="POST" action="{{ route('admin.checklists.destroy', $checklist) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete checklist?')"><i class="fa fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No checklists yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-2">
            {{ $checklists->links() }}
        </div>
    </div>
</div>
@endsection
