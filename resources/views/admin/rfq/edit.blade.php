@extends('admin.layouts.app')

@section('title', 'Edit RFQ')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Edit RFQ</h5>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.rfq.update', $rfq->rfq_id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-select" required>
                            @foreach($projects as $project)
                                <option value="{{ $project->proj_id }}" @selected($project->proj_id == $rfq->rfq_project_id)>{{ $project->proj_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" value="{{ old('title', $rfq->rfq_title) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $rfq->rfq_description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due date</label>
                        <input type="date" class="form-control" name="due_date" value="{{ old('due_date', optional($rfq->rfq_due_date)->format('Y-m-d')) }}" required>
                    </div>

                    <div class="d-grid mt-3">
                        <button class="btn btn-primary" type="submit">Update RFQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
