@extends('layouts.admin')

@section('title', 'Edit Item - ' . $item->item_name)

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Item</h1>
            <p class="text-muted mb-0">Update details for <strong>{{ $item->item_name }}</strong> ({{ $item->item_code }}).</p>
        </div>
        <a href="{{ route('admin.item.show', $item->item_id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Item
        </a>
    </div>

    <!-- Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Item Details</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.item.update', $item->item_id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="item_code">Item Code <span class="text-danger">*</span></label>
                            <input type="text" name="item_code" id="item_code"
                                   class="form-control @error('item_code') is-invalid @enderror"
                                   value="{{ old('item_code', $item->item_code) }}" required>
                            @error('item_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label" for="item_name">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="item_name" id="item_name"
                                   class="form-control @error('item_name') is-invalid @enderror"
                                   value="{{ old('item_name', $item->item_name) }}" required>
                            @error('item_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label" for="item_description">Description</label>
                    <textarea name="item_description" id="item_description" rows="3"
                              class="form-control @error('item_description') is-invalid @enderror">{{ old('item_description', $item->item_description) }}</textarea>
                    @error('item_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="form-label" for="item_cat_ms">Category <span class="text-danger">*</span></label>
                            <select name="item_cat_ms" id="item_cat_ms"
                                    class="form-select @error('item_cat_ms') is-invalid @enderror" required>
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->icat_id }}" @selected(old('item_cat_ms', $item->item_cat_ms) == $category->icat_id)>
                                        {{ $category->icat_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('item_cat_ms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="form-label" for="item_ccode_ms">Cost Code</label>
                            <select name="item_ccode_ms" id="item_ccode_ms"
                                    class="form-select @error('item_ccode_ms') is-invalid @enderror">
                                <option value="">-- Select Cost Code --</option>
                                @foreach($costCodes as $cc)
                                    <option value="{{ $cc->cc_id }}" @selected(old('item_ccode_ms', $item->item_ccode_ms) == $cc->cc_id)>
                                        {{ $cc->cc_no }} - {{ $cc->cc_description }}
                                    </option>
                                @endforeach
                            </select>
                            @error('item_ccode_ms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="form-label" for="uom_id">Unit of Measure</label>
                            <select name="uom_id" id="uom_id"
                                    class="form-select @error('uom_id') is-invalid @enderror">
                                <option value="">-- Select UOM --</option>
                                @foreach($uoms as $uom)
                                    <option value="{{ $uom->uom_id }}" @selected(old('uom_id', $item->item_unit_ms) == $uom->uom_id)>
                                        {{ $uom->uom_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('uom_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="form-label" for="item_status">Status</label>
                            <select name="item_status" id="item_status"
                                    class="form-select @error('item_status') is-invalid @enderror">
                                <option value="1" @selected(old('item_status', $item->item_status) == 1)>Active</option>
                                <option value="0" @selected(old('item_status', $item->item_status) == 0)>Inactive</option>
                            </select>
                            @error('item_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-1"></i> Update Item
                    </button>
                    <a href="{{ route('admin.item.show', $item->item_id) }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Placeholder for any client-side validation or enhancements
</script>
@endpush
