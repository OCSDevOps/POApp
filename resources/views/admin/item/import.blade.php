@extends('layouts.admin')

@section('title', 'Import Items')

@section('content')
<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Import errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Import Items</h1>
            <p class="text-muted mb-0">Bulk import items from a CSV file.</p>
        </div>
        <a href="{{ route('admin.item.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Items
        </a>
    </div>

    <!-- Instructions Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">CSV Format Instructions</h6>
        </div>
        <div class="card-body">
            <p>Prepare your CSV file with the following columns. The first row must contain the column headers exactly as shown below.</p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Column</th>
                            <th>Required</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>item_code</code></td>
                            <td><span class="badge bg-danger">Required</span></td>
                            <td>Unique item code identifier</td>
                        </tr>
                        <tr>
                            <td><code>item_name</code></td>
                            <td><span class="badge bg-danger">Required</span></td>
                            <td>Name of the item</td>
                        </tr>
                        <tr>
                            <td><code>category_id</code></td>
                            <td><span class="badge bg-danger">Required</span></td>
                            <td>ID of the item category (must match existing category)</td>
                        </tr>
                        <tr>
                            <td><code>cost_code_id</code></td>
                            <td><span class="badge bg-secondary">Optional</span></td>
                            <td>ID of the cost code (must match existing cost code)</td>
                        </tr>
                        <tr>
                            <td><code>description</code></td>
                            <td><span class="badge bg-secondary">Optional</span></td>
                            <td>Item description text</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <p class="mb-1"><strong>Example:</strong></p>
                <pre class="bg-light p-3 rounded"><code>item_code,item_name,category_id,cost_code_id,description
ITM-001,Concrete Mix 50lb,3,12,Standard 50lb bag concrete mix
ITM-002,Rebar #4 x 20ft,3,,Steel reinforcement bar</code></pre>
            </div>
        </div>
    </div>

    <!-- Upload Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Upload File</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.item.import.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group mb-3">
                    <label class="form-label" for="file">CSV File <span class="text-danger">*</span></label>
                    <input type="file" name="file" id="file"
                           class="form-control @error('file') is-invalid @enderror"
                           accept=".csv,.txt" required>
                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Accepted formats: .csv, .txt (comma-separated values)</div>
                </div>

                <hr class="my-4">

                <div class="d-flex">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-file-import me-1"></i> Import Items
                    </button>
                    <a href="{{ route('admin.item.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Placeholder for any import-page enhancements
</script>
@endpush
