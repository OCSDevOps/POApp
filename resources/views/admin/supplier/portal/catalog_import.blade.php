@extends('layouts.admin')

@section('title', 'Import Catalog')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <h4 class="mb-1">Import Catalog - {{ $supplier->sup_name ?? '' }}</h4>
        <p class="text-muted mb-0">CSV columns: item_code, sku_no, uom_id, price, details (optional).</p>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.supplier.catalog.import', $supplier->sup_id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">CSV File</label>
                        <input type="file" name="file" class="form-control" accept=".csv,text/csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Import</button>
                    <a href="{{ route('admin.supplier.catalog.index', $supplier->sup_id) }}" class="btn btn-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
