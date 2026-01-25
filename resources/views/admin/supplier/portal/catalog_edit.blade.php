@extends('layouts.admin')

@section('title', 'Edit Catalog Item')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <h4 class="mb-1">Edit Catalog Item - {{ $supplier->sup_name ?? '' }}</h4>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.supplier.catalog.update', [$supplier->sup_id, $catalogEntry->supcat_id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Item</label>
                            <input type="text" class="form-control" value="{{ $catalogEntry->item->item_name ?? $catalogEntry->supcat_item_code }}" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku_no" class="form-control" value="{{ $catalogEntry->supcat_sku_no }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">UOM</label>
                            <select name="uom_id" class="form-select" required>
                                @foreach($uoms as $uom)
                                    <option value="{{ $uom->uom_id }}" @selected($uom->uom_id == $catalogEntry->supcat_uom)>{{ $uom->uom_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" value="{{ $catalogEntry->supcat_price }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Effective Date</label>
                            <input type="date" name="effective_date" class="form-control" value="{{ $catalogEntry->supcat_lastdate }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Details</label>
                            <textarea name="details" class="form-control" rows="3">{{ $catalogEntry->supcat_details }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1" @selected($catalogEntry->supcat_status == 1)>Active</option>
                                <option value="0" @selected($catalogEntry->supcat_status == 0)>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('admin.supplier.catalog.index', $supplier->sup_id) }}" class="btn btn-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
