@extends('supplier.layouts.app')

@section('title', 'Import Pricing')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Bulk Import Pricing</h5>
                <p class="text-muted small mb-3">Upload a CSV with columns: item_id, supplier_id (ignored), project_id (optional), unit_price, effective_from (YYYY-MM-DD), effective_to (optional).</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('supplier.pricing.import.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">CSV file</label>
                        <input type="file" name="csv" class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-primary" type="submit">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
