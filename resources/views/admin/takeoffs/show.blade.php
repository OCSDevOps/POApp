@extends('layouts.admin')

@section('title', 'Takeoff Details - ' . $takeoff->to_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            {{ $takeoff->to_number }} — {{ $takeoff->to_title }}
            <span class="badge bg-{{ $takeoff->status_badge }}">{{ $takeoff->status_label }}</span>
        </h1>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.takeoffs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
        @if(in_array($takeoff->to_status, [1, 3]))
            <a href="{{ route('admin.takeoffs.edit', $takeoff->to_id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <form method="POST" action="{{ route('admin.takeoffs.finalize', $takeoff->to_id) }}" class="d-inline"
                  onsubmit="return confirm('Are you sure you want to finalize this takeoff? This action cannot be undone.')">
                @csrf
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check-circle me-1"></i> Finalize
                </button>
            </form>
        @endif
        @if($takeoff->to_status == 4)
            <form method="POST" action="{{ route('admin.takeoffs.convert-to-po', $takeoff->to_id) }}" class="d-inline"
                  onsubmit="return confirm('Convert this takeoff to a Purchase Order? A new PO will be created from the takeoff items.')">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-file-invoice me-1"></i> Convert to PO
                </button>
            </form>
        @endif
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Info Card --}}
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-info-circle me-1"></i> Takeoff Information
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="text-muted small">Project</label>
                <p class="mb-0 fw-semibold">{{ $takeoff->project->proj_name ?? 'N/A' }} ({{ $takeoff->project->proj_number ?? 'N/A' }})</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="text-muted small">Created</label>
                <p class="mb-0">{{ $takeoff->to_createdate ? $takeoff->to_createdate->format('M d, Y h:i A') : 'N/A' }}</p>
            </div>
            <div class="col-md-4 mb-3">
                <label class="text-muted small">Status</label>
                <p class="mb-0">
                    <span class="badge bg-{{ $takeoff->status_badge }}">{{ $takeoff->status_label }}</span>
                </p>
            </div>
            @if($takeoff->to_description)
                <div class="col-md-12 mb-3">
                    <label class="text-muted small">Description</label>
                    <p class="mb-0">{{ $takeoff->to_description }}</p>
                </div>
            @endif
            @if($takeoff->to_status == 4)
                <div class="col-md-4 mb-3">
                    <label class="text-muted small">Finalized Date</label>
                    <p class="mb-0">{{ $takeoff->to_finalized_date ? $takeoff->to_finalized_date->format('M d, Y h:i A') : 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="text-muted small">Finalized By</label>
                    <p class="mb-0">{{ $takeoff->finalizedBy->name ?? 'N/A' }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-3" id="takeoffTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="items-tab" data-bs-toggle="tab" data-bs-target="#items-pane"
                type="button" role="tab" aria-controls="items-pane" aria-selected="true">
            <i class="fas fa-list me-1"></i> Items
            <span class="badge bg-secondary ms-1">{{ $takeoff->activeItems->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="drawings-tab" data-bs-toggle="tab" data-bs-target="#drawings-pane"
                type="button" role="tab" aria-controls="drawings-pane" aria-selected="false">
            <i class="fas fa-drafting-compass me-1"></i> Drawings
            <span class="badge bg-secondary ms-1">{{ $takeoff->activeDrawings->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary-pane"
                type="button" role="tab" aria-controls="summary-pane" aria-selected="false">
            <i class="fas fa-chart-pie me-1"></i> Summary
        </button>
    </li>
</ul>

<div class="tab-content" id="takeoffTabsContent">

    {{-- Tab 1: Items --}}
    <div class="tab-pane fade show active" id="items-pane" role="tabpanel" aria-labelledby="items-tab">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-list me-1"></i> Takeoff Items</span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered datatable mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Source</th>
                            <th>Description</th>
                            <th>Item Code</th>
                            <th class="text-end">Qty</th>
                            <th>UOM</th>
                            <th class="text-end">Unit Price ($)</th>
                            <th class="text-end">Subtotal ($)</th>
                            <th>Confidence</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($takeoff->activeItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($item->tod_source === 'ai')
                                        <span class="badge bg-info">AI</span>
                                    @else
                                        <span class="badge bg-secondary">Manual</span>
                                    @endif
                                </td>
                                <td>{{ $item->tod_description }}</td>
                                <td>{{ $item->tod_item_code ?? '—' }}</td>
                                <td class="text-end">{{ $item->tod_quantity }}</td>
                                <td>{{ $item->unitOfMeasure->uom_name ?? '—' }}</td>
                                <td class="text-end">${{ number_format($item->tod_unit_price, 2) }}</td>
                                <td class="text-end">${{ number_format($item->tod_quantity * $item->tod_unit_price, 2) }}</td>
                                <td>
                                    @if($item->tod_source === 'ai')
                                        @php
                                            $confidence = $item->tod_ai_confidence;
                                            if ($confidence >= 80) {
                                                $confBadge = 'success';
                                            } elseif ($confidence >= 50) {
                                                $confBadge = 'warning';
                                            } else {
                                                $confBadge = 'danger';
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $confBadge }}">{{ $confidence }}%</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $item->tod_notes ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted">No items yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($takeoff->activeItems->count() > 0)
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="7" class="text-end"><strong>Total Items: {{ $takeoff->activeItems->count() }}</strong></td>
                                <td class="text-end">
                                    <strong>${{ number_format($takeoff->activeItems->sum(function($item) { return $item->tod_quantity * $item->tod_unit_price; }), 2) }}</strong>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- Tab 2: Drawings --}}
    <div class="tab-pane fade" id="drawings-pane" role="tabpanel" aria-labelledby="drawings-tab">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-drafting-compass me-1"></i> Drawings</span>
            </div>
            <div class="card-body">
                @if($takeoff->activeDrawings->count() > 0)
                    <div class="row">
                        @foreach($takeoff->activeDrawings as $drawing)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            @if(Str::endsWith(strtolower($drawing->tdr_original_name), '.pdf'))
                                                <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                            @else
                                                <i class="fas fa-file-image fa-3x text-primary"></i>
                                            @endif
                                        </div>
                                        <h6 class="card-title text-truncate" title="{{ $drawing->tdr_original_name }}">
                                            {{ $drawing->tdr_original_name }}
                                        </h6>
                                        <p class="text-muted small mb-2">{{ $drawing->file_size_formatted }}</p>
                                        <p class="mb-3">
                                            <span class="badge bg-{{ $drawing->ai_status_badge }}">{{ ucfirst($drawing->tdr_ai_status) }}</span>
                                        </p>
                                        @if($drawing->tdr_ai_status === 'failed' && $drawing->tdr_ai_error)
                                            <p class="text-danger small mb-3">{{ $drawing->tdr_ai_error }}</p>
                                        @endif
                                        <div class="btn-group-vertical w-100">
                                            <a href="{{ route('admin.takeoffs.download-drawing', [$takeoff->to_id, $drawing->tdr_id]) }}"
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-download me-1"></i> Download
                                            </a>
                                            @if($aiEnabled && in_array($drawing->tdr_ai_status, ['pending', 'failed']))
                                                <form method="POST"
                                                      action="{{ route('admin.takeoffs.process-drawing', [$takeoff->to_id, $drawing->tdr_id]) }}"
                                                      class="d-grid">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-success btn-sm">
                                                        <i class="fas fa-robot me-1"></i> Process with AI
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST"
                                                  action="{{ route('admin.takeoffs.delete-drawing', [$takeoff->to_id, $drawing->tdr_id]) }}"
                                                  onsubmit="return confirm('Are you sure you want to delete this drawing?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                    <i class="fas fa-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">No drawings uploaded.</p>
                @endif
            </div>
        </div>

        {{-- Upload More Drawings --}}
        @if(in_array($takeoff->to_status, [1, 3]))
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-upload me-1"></i> Upload More Drawings
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.takeoffs.upload-drawings', $takeoff->to_id) }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-8 mb-3">
                                <label for="drawings" class="form-label">Select Files</label>
                                <input type="file" name="drawings[]" id="drawings" class="form-control"
                                       multiple accept=".pdf,.jpg,.jpeg,.png,.tiff,.tif,.dwg">
                                <small class="text-muted">Accepted formats: PDF, JPG, PNG, TIFF, DWG</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-upload me-1"></i> Upload
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    {{-- Tab 3: Summary --}}
    <div class="tab-pane fade" id="summary-pane" role="tabpanel" aria-labelledby="summary-tab">

        {{-- Stat Cards --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-dollar-sign me-1"></i> Totals
                    </div>
                    <div class="card-body">
                        @php
                            $subtotal = $takeoff->activeItems->sum(function($item) {
                                return $item->tod_quantity * $item->tod_unit_price;
                            });
                            $tax = $takeoff->to_total_tax ?? 0;
                            $grandTotal = $subtotal + $tax;
                        @endphp
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <strong>${{ number_format($subtotal, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tax</span>
                            <strong>${{ number_format($tax, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2">
                            <span class="text-muted fw-bold">Grand Total</span>
                            <strong class="text-primary fs-5">${{ number_format($grandTotal, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-cubes me-1"></i> Item Sources
                    </div>
                    <div class="card-body">
                        @php
                            $aiItems = $takeoff->activeItems->where('tod_source', 'ai')->count();
                            $manualItems = $takeoff->activeItems->where('tod_source', 'manual')->count();
                            $totalItems = $takeoff->activeItems->count();
                        @endphp
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted"><i class="fas fa-robot me-1"></i> AI Items</span>
                            <strong>{{ $aiItems }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted"><i class="fas fa-pencil-alt me-1"></i> Manual Items</span>
                            <strong>{{ $manualItems }}</strong>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2">
                            <span class="text-muted fw-bold">Total Items</span>
                            <strong>{{ $totalItems }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cost Code Breakdown --}}
        <div class="card">
            <div class="card-header">
                <i class="fas fa-sitemap me-1"></i> Cost Code Breakdown
            </div>
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Cost Code</th>
                            <th>Description</th>
                            <th class="text-end">Items</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grouped = $takeoff->activeItems->groupBy(function($item) {
                                return $item->tod_cost_code_id ?? 'unassigned';
                            });
                        @endphp
                        @forelse($grouped as $costCodeId => $groupItems)
                            <tr>
                                @if($costCodeId === 'unassigned')
                                    <td><em class="text-muted">Unassigned</em></td>
                                    <td><em class="text-muted">No cost code</em></td>
                                @else
                                    @php
                                        $costCode = $groupItems->first()->costCode ?? null;
                                    @endphp
                                    <td>{{ $costCode->cc_no ?? 'N/A' }}</td>
                                    <td>{{ $costCode->cc_description ?? 'N/A' }}</td>
                                @endif
                                <td class="text-end">{{ $groupItems->count() }}</td>
                                <td class="text-end">
                                    ${{ number_format($groupItems->sum(function($item) { return $item->tod_quantity * $item->tod_unit_price; }), 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No items to display.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
