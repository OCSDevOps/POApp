@extends('layouts.admin')

@section('title', 'Packages')

@section('content')
<div class="row">
    <div class="col-12 d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Item Packages</h4>
            <p class="text-muted mb-0">Pre-built bundles of items for quick PO/RFQ creation.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPackageModal">
            <i class="fa fa-plus me-1"></i> New Package
        </button>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">Packages</div>
            <div class="card-body table-responsive">
                <table class="table table-striped datatable align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Items</th>
                            <th>Total Qty</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packages as $pkg)
                            <tr>
                                <td>{{ $pkg->ipack_id }}</td>
                                <td>{{ $pkg->ipack_name }}</td>
                                <td>{{ $pkg->ipack_totalitem }}</td>
                                <td>{{ $pkg->ipack_total_qty }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary me-1 edit-btn"
                                            data-id="{{ $pkg->ipack_id }}"
                                            data-name="{{ $pkg->ipack_name }}"
                                            data-details="{{ $pkg->ipack_details }}"
                                            data-items='@json($pkg->details->map(fn($d)=>["item"=>$d->ipdetail_item_ms,"qty"=>$d->ipdetail_quantity,"info"=>$d->ipdetail_info]))'>
                                        <i class="fa fa-pen"></i>
                                    </button>
                                    <form action="{{ route('admin.packages.destroy', $pkg) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Archive this package?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createPackageModal" tabindex="-1" aria-labelledby="createPackageLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.packages.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createPackageLabel">New Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.packages.partials.form-fields')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editPackageModal" tabindex="-1" aria-labelledby="editPackageLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editPackageForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editPackageLabel">Edit Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.packages.partials.form-fields')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function renderItems(container, items) {
        container.empty();
        items.forEach((line, idx) => {
            container.append(`
                <div class="row g-2 align-items-end pkg-line mb-2">
                    <div class="col-md-6">
                        <label class="form-label">Item</label>
                        <select name="items[${idx}][item_id]" class="form-select" required>
                            @foreach($items as $item)
                                <option value="{{ $item->item_id }}">{{ $item->item_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Qty</label>
                        <input type="number" name="items[${idx}][quantity]" class="form-control" min="1" required value="${line.qty ?? 1}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Info</label>
                        <input type="text" name="items[${idx}][info]" class="form-control" value="${line.info ?? ''}">
                    </div>
                </div>
            `);
        });
    }

    $(function () {
        const createContainer = $('#createPackageModal .items-container');
        renderItems(createContainer, [{qty:1}]);

        $('#createPackageModal').on('click', '.add-line', function () {
            const lines = createContainer.find('.pkg-line').length;
            renderItems(createContainer, Array.from({length: lines + 1}, (_, i) => ({qty:1})));
        });

        const editContainer = $('#editPackageModal .items-container');

        $('.edit-btn').on('click', function () {
            const data = $(this).data();
            $('#ipack_name').val(data.name);
            $('#ipack_details').val(data.details);
            const items = data.items || [];
            const mapped = items.map(i => ({qty: i.qty, info: i.info, item: i.item}));
            renderItems(editContainer, mapped.length ? mapped : [{qty:1}]);
            // Set selected options
            editContainer.find('select').each(function (idx) {
                $(this).val(mapped[idx]?.item ?? '');
            });

            const action = "{{ route('admin.packages.update', ':id') }}".replace(':id', data.id);
            $('#editPackageForm').attr('action', action);

            $('#editPackageModal').modal('show');
        });

        $('#editPackageModal').on('click', '.add-line', function () {
            const lines = editContainer.find('.pkg-line').length;
            renderItems(editContainer, Array.from({length: lines + 1}, (_, i) => ({qty:1})));
        });
    });
</script>
@endpush
