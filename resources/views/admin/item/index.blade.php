@extends('layouts.admin')

@section('title', 'Items')

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

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Items</h1>
            <p class="text-muted mb-0">Manage your item catalog, pricing, and categorization.</p>
        </div>
        <div>
            <a href="{{ route('admin.item.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Item
            </a>
            <a href="{{ route('admin.item.import') }}" class="btn btn-outline-secondary">
                <i class="fas fa-file-import me-1"></i> Import Items
            </a>
            <a href="{{ route('admin.item.export') }}" class="btn btn-outline-success">
                <i class="fas fa-file-export me-1"></i> Export
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.item.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->icat_id }}" @selected(request('category') == $category->icat_id)>
                                    {{ $category->icat_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cost Code</label>
                        <select name="cost_code" class="form-select">
                            <option value="">All Cost Codes</option>
                            @foreach($costCodes as $cc)
                                <option value="{{ $cc->cc_id }}" @selected(request('cost_code') == $cc->cc_id)>
                                    {{ $cc->cc_no }} - {{ $cc->cc_description }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="1" @selected(request('status') === '1')>Active</option>
                            <option value="0" @selected(request('status') === '0')>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Code or name..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary me-1">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.item.index') }}" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Items Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Item List</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle" id="itemsTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Cost Code</th>
                            <th>UOM</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td><strong>{{ $item->item_code }}</strong></td>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->category->icat_name ?? '-' }}</td>
                                <td>
                                    @if($item->costCode)
                                        {{ $item->costCode->cc_no }} - {{ $item->costCode->cc_description }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->unitOfMeasure->uom_name ?? '-' }}</td>
                                <td>
                                    @if($item->item_status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.item.show', $item->item_id) }}" class="btn btn-sm btn-outline-info me-1" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.item.edit', $item->item_id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" title="Delete"
                                            data-url="{{ route('admin.item.destroy', $item->item_id) }}" data-name="{{ $item->item_name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</div>
@include('partials.delete-modal')
@endsection

@push('scripts')
<script>
    $(function () {
        if ($.fn.DataTable) {
            $('#itemsTable').DataTable({
                paging: false,
                info: false,
                searching: false,
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]
            });
        }
    });
</script>
@endpush
