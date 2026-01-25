@extends('layouts.admin')

@section('title', 'Supplier Catalog')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Catalog - {{ $supplier->sup_name ?? '' }}</h4>
            <p class="text-muted mb-0">Manage items, pricing, and availability.</p>
        </div>
        <div>
            <a class="btn btn-outline-secondary me-2" href="{{ route('admin.supplier.performance', $supplier->sup_id) }}">Performance</a>
            <a class="btn btn-outline-secondary me-2" href="{{ route('admin.supplier.catalog.import', $supplier->sup_id) }}">Import</a>
            <a class="btn btn-outline-secondary me-2" href="{{ route('admin.supplier.catalog.export', $supplier->sup_id) }}">Export</a>
            <a class="btn btn-primary" href="{{ route('admin.supplier.catalog.create', $supplier->sup_id) }}"><i class="fa fa-plus me-1"></i> Add Item</a>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Item Code</th>
                            <th>Item</th>
                            <th>SKU</th>
                            <th>UOM</th>
                            <th>Price</th>
                            <th>Updated</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($catalog as $entry)
                            <tr>
                                <td>{{ $entry->supcat_item_code }}</td>
                                <td>{{ $entry->item->item_name ?? '' }}</td>
                                <td>{{ $entry->supcat_sku_no }}</td>
                                <td>{{ $entry->unitOfMeasure->uom_name ?? '' }}</td>
                                <td>{{ $entry->supcat_price }}</td>
                                <td>{{ $entry->supcat_lastdate }}</td>
                                <td>
                                    @if($entry->supcat_status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.supplier.catalog.edit', [$supplier->sup_id, $entry->supcat_id]) }}"><i class="fa fa-pen"></i></a>
                                    <form action="{{ route('admin.supplier.catalog.destroy', [$supplier->sup_id, $entry->supcat_id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this catalog item?')"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">No catalog entries.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $catalog->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
