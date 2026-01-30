@extends('supplier.layouts.app')

@section('title', 'Add Pricing')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Add Item Pricing</h5>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('supplier.pricing.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Item</label>
                        <select name="item_id" class="form-select" required>
                            <option value="">Select item</option>
                            @foreach($items as $item)
                                <option value="{{ $item->item_id }}">{{ $item->item_name }} ({{ $item->item_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Project (optional)</label>
                        <select name="project_id" class="form-select">
                            <option value="">All projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->proj_id }}">{{ $project->proj_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit price</label>
                        <input type="number" step="0.01" name="unit_price" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">Effective from</label>
                            <input type="date" name="effective_from" class="form-control" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Effective to (optional)</label>
                            <input type="date" name="effective_to" class="form-control">
                        </div>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-primary" type="submit">Save Pricing</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
