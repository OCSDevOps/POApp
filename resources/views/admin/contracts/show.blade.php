@extends('layouts.admin')

@section('title', 'Contract: ' . $contract->contract_number)

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1 font-weight-bold text-primary">
                <i class="fas fa-file-contract me-1"></i>
                {{ $contract->contract_number }} &mdash; {{ $contract->title }}
            </h5>
            <div>
                @switch($contract->status)
                    @case('draft')
                        <span class="badge bg-secondary fs-6">Draft</span>
                        @break
                    @case('pending')
                        <span class="badge bg-warning text-dark fs-6">Pending</span>
                        @break
                    @case('approved')
                        <span class="badge bg-info fs-6">Approved</span>
                        @break
                    @case('active')
                        <span class="badge bg-success fs-6">Active</span>
                        @break
                    @case('completed')
                        <span class="badge bg-primary fs-6">Completed</span>
                        @break
                    @case('cancelled')
                        <span class="badge bg-danger fs-6">Cancelled</span>
                        @break
                    @case('closed')
                        <span class="badge bg-dark fs-6">Closed</span>
                        @break
                    @default
                        <span class="badge bg-secondary fs-6">{{ ucfirst($contract->status) }}</span>
                @endswitch
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if(in_array($contract->status, ['draft', 'approved']))
                <a href="{{ route('admin.contracts.edit', $contract->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            @endif

            @if(in_array($contract->status, ['draft', 'approved']))
                <form method="POST" action="{{ route('admin.contracts.updatestatus', $contract->id) }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="action" value="activate">
                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Activate this contract?');">
                        <i class="fas fa-check-circle me-1"></i> Activate
                    </button>
                </form>
            @endif

            @if($contract->status === 'active')
                <form method="POST" action="{{ route('admin.contracts.updatestatus', $contract->id) }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="action" value="complete">
                    <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Mark this contract as completed?');">
                        <i class="fas fa-flag-checkered me-1"></i> Complete
                    </button>
                </form>
            @endif

            @if(!in_array($contract->status, ['completed', 'closed', 'cancelled']))
                <form method="POST" action="{{ route('admin.contracts.updatestatus', $contract->id) }}" class="d-inline">
                    @csrf
                    <input type="hidden" name="action" value="cancel">
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this contract? This action cannot be undone.');">
                        <i class="fas fa-ban me-1"></i> Cancel
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3" id="contractTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview"
                    type="button" role="tab" aria-controls="overview" aria-selected="true">
                <i class="fas fa-info-circle me-1"></i> Overview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="change-orders-tab" data-bs-toggle="tab" data-bs-target="#change-orders"
                    type="button" role="tab" aria-controls="change-orders" aria-selected="false">
                <i class="fas fa-exchange-alt me-1"></i> Change Orders
                @if($contract->changeOrders && $contract->changeOrders->count())
                    <span class="badge bg-secondary ms-1">{{ $contract->changeOrders->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices"
                    type="button" role="tab" aria-controls="invoices" aria-selected="false">
                <i class="fas fa-file-invoice-dollar me-1"></i> Invoices
                @if($contract->invoices && $contract->invoices->count())
                    <span class="badge bg-secondary ms-1">{{ $contract->invoices->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents"
                    type="button" role="tab" aria-controls="documents" aria-selected="false">
                <i class="fas fa-paperclip me-1"></i> Documents
                @if($contract->documents && $contract->documents->count())
                    <span class="badge bg-secondary ms-1">{{ $contract->documents->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="compliance-tab" data-bs-toggle="tab" data-bs-target="#compliance"
                    type="button" role="tab" aria-controls="compliance" aria-selected="false">
                <i class="fas fa-shield-alt me-1"></i> Compliance
            </button>
        </li>
    </ul>

    <div class="tab-content" id="contractTabsContent">

        {{-- ============================================= --}}
        {{-- Tab 1: Overview --}}
        {{-- ============================================= --}}
        <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">

            {{-- Financial Summary Cards --}}
            <div class="row mb-4">
                <div class="col-xl-2 col-md-4 mb-3">
                    <div class="card stat-card primary h-100">
                        <div class="card-body py-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Original Value</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">${{ number_format($summary['original_value'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 mb-3">
                    <div class="card stat-card info h-100">
                        <div class="card-body py-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Revised Value</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">${{ number_format($summary['revised_value'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 mb-3">
                    <div class="card stat-card warning h-100">
                        <div class="card-body py-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Invoiced</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">${{ number_format($summary['invoiced'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 mb-3">
                    <div class="card stat-card success h-100">
                        <div class="card-body py-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">${{ number_format($summary['paid'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 mb-3">
                    <div class="card stat-card danger h-100">
                        <div class="card-body py-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Retention Balance</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">${{ number_format($summary['retention_balance'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 mb-3">
                    <div class="card stat-card primary h-100">
                        <div class="card-body py-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Completion</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['completion_percentage'] ?? 0, 1) }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contract Details --}}
            <div class="row">
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-info-circle me-1"></i> Contract Details
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th style="width: 35%;">Contract #</th>
                                    <td>{{ $contract->contract_number }}</td>
                                </tr>
                                <tr>
                                    <th>Project</th>
                                    <td>{{ $contract->project->proj_name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Subcontractor</th>
                                    <td>{{ $contract->supplier->sup_name ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Cost Code</th>
                                    <td>{{ $contract->costCode->cc_description ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <td>{{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('m/d/Y') : '—' }}</td>
                                </tr>
                                <tr>
                                    <th>End Date</th>
                                    <td>{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('m/d/Y') : '—' }}</td>
                                </tr>
                                <tr>
                                    <th>Retention %</th>
                                    <td>{{ $contract->retention_percentage }}%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-align-left me-1"></i> Description
                            </h6>
                        </div>
                        <div class="card-body">
                            <p>{{ $contract->description ?: 'No description provided.' }}</p>
                        </div>
                    </div>

                    @if($contract->scope_of_work)
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-clipboard-list me-1"></i> Scope of Work
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $contract->scope_of_work }}</p>
                            </div>
                        </div>
                    @endif

                    @if($contract->terms_conditions)
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-gavel me-1"></i> Terms & Conditions
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $contract->terms_conditions }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- Tab 2: Change Orders --}}
        {{-- ============================================= --}}
        <div class="tab-pane fade" id="change-orders" role="tabpanel" aria-labelledby="change-orders-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-exchange-alt me-1"></i> Change Orders
                    </h6>
                    <a href="{{ route('admin.contract-change-orders.create', ['contract_id' => $contract->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> New Change Order
                    </a>
                </div>
                <div class="card-body">
                    @if($contract->changeOrders && $contract->changeOrders->count())
                        <div class="table-responsive">
                            <table class="table table-hover datatable" id="changeOrdersTable" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>CCO #</th>
                                        <th class="text-end">Amount</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contract->changeOrders as $cco)
                                        <tr>
                                            <td><strong>{{ $cco->cco_number }}</strong></td>
                                            <td class="text-end">
                                                <span class="{{ $cco->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $cco->amount >= 0 ? '+' : '' }}${{ number_format(abs($cco->amount), 2) }}
                                                </span>
                                            </td>
                                            <td>{{ Str::limit($cco->description, 60) }}</td>
                                            <td>
                                                @switch($cco->status)
                                                    @case('draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                        @break
                                                    @case('pending')
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge bg-success">Approved</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($cco->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $cco->created_at ? $cco->created_at->format('m/d/Y') : '—' }}</td>
                                            <td>
                                                <a href="{{ route('admin.contract-change-orders.show', $cco->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-1"></i> No change orders have been created for this contract.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- Tab 3: Invoices --}}
        {{-- ============================================= --}}
        <div class="tab-pane fade" id="invoices" role="tabpanel" aria-labelledby="invoices-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-invoice-dollar me-1"></i> Invoices
                    </h6>
                    <a href="{{ route('admin.contracts.invoices.create', $contract->id) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> New Invoice
                    </a>
                </div>
                <div class="card-body">
                    @if($contract->invoices && $contract->invoices->count())
                        <div class="table-responsive">
                            <table class="table table-hover datatable" id="invoicesTable" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th class="text-end">Gross Amount</th>
                                        <th class="text-end">Retention</th>
                                        <th class="text-end">Net</th>
                                        <th class="text-end">Paid</th>
                                        <th class="text-end">Balance</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contract->invoices as $invoice)
                                        <tr>
                                            <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                            <td>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('m/d/Y') : '—' }}</td>
                                            <td class="text-end">${{ number_format($invoice->gross_amount ?? 0, 2) }}</td>
                                            <td class="text-end">${{ number_format($invoice->retention_amount ?? 0, 2) }}</td>
                                            <td class="text-end">${{ number_format($invoice->net_amount ?? 0, 2) }}</td>
                                            <td class="text-end">${{ number_format($invoice->paid_amount ?? 0, 2) }}</td>
                                            <td class="text-end">
                                                <strong>${{ number_format(($invoice->net_amount ?? 0) - ($invoice->paid_amount ?? 0), 2) }}</strong>
                                            </td>
                                            <td>
                                                @switch($invoice->status)
                                                    @case('draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                        @break
                                                    @case('submitted')
                                                        <span class="badge bg-warning text-dark">Submitted</span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge bg-info">Approved</span>
                                                        @break
                                                    @case('paid')
                                                        <span class="badge bg-success">Paid</span>
                                                        @break
                                                    @case('partial')
                                                        <span class="badge bg-warning text-dark">Partial</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.contracts.invoices.show', [$contract->id, $invoice->id]) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-1"></i> No invoices have been created for this contract.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Release Retention --}}
            @if(($summary['retention_balance'] ?? 0) > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-unlock-alt me-1"></i> Release Retention
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">
                            Current retention balance: <strong class="text-danger">${{ number_format($summary['retention_balance'], 2) }}</strong>
                        </p>
                        <form method="POST" action="{{ route('admin.contracts.release-retention', $contract->id) }}"
                              class="row g-3 align-items-end" onsubmit="return confirm('Release this retention amount?');">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label" for="retention_amount">Amount to Release <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="retention_amount" name="amount"
                                           min="0.01" max="{{ $summary['retention_balance'] }}" step="0.01"
                                           placeholder="0.00" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-unlock me-1"></i> Release
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        {{-- ============================================= --}}
        {{-- Tab 4: Documents --}}
        {{-- ============================================= --}}
        <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">

            {{-- Upload Form --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-upload me-1"></i> Upload Documents
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.contracts.upload-documents', $contract->id) }}"
                          enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">Files</label>
                                <input type="file" class="form-control" name="documents[]" multiple required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Document Type</label>
                                <select class="form-select" name="document_type">
                                    <option value="">-- Select Type --</option>
                                    <option value="COI">COI</option>
                                    <option value="Signed Contract">Signed Contract</option>
                                    <option value="W-9">W-9</option>
                                    <option value="Lien Waiver">Lien Waiver</option>
                                    <option value="Insurance Certificate">Insurance Certificate</option>
                                    <option value="License">License</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-1"></i> Upload
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Documents List --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-folder-open me-1"></i> Contract Documents
                    </h6>
                </div>
                <div class="card-body">
                    @if($contract->documents && $contract->documents->count())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contract->documents as $document)
                                        <tr>
                                            <td>
                                                <i class="fas fa-file me-1 text-muted"></i>
                                                {{ $document->original_name ?? $document->file_name }}
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $document->document_type ?? '—' }}</span>
                                            </td>
                                            <td>
                                                @if($document->file_size)
                                                    @if($document->file_size >= 1048576)
                                                        {{ number_format($document->file_size / 1048576, 1) }} MB
                                                    @else
                                                        {{ number_format($document->file_size / 1024, 1) }} KB
                                                    @endif
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>{{ $document->created_at ? $document->created_at->format('m/d/Y') : '—' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.contracts.download-document', [$contract->id, $document->id]) }}"
                                                       class="btn btn-outline-primary" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    <form method="POST"
                                                          action="{{ route('admin.contracts.delete-document', [$contract->id, $document->id]) }}"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Delete this document?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-1"></i> No documents have been uploaded for this contract.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- Tab 5: Compliance --}}
        {{-- ============================================= --}}
        <div class="tab-pane fade" id="compliance" role="tabpanel" aria-labelledby="compliance-tab">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shield-alt me-1"></i> Compliance Status
                    </h6>
                    @if($contract->supplier)
                        <a href="{{ route('admin.suppliers.show', $contract->supplier->sup_id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-external-link-alt me-1"></i> Full Supplier Compliance
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if(isset($complianceStatus))
                        <div class="mb-4">
                            <strong>Overall Status:</strong>
                            @if($complianceStatus['is_compliant'] ?? false)
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check-circle me-1"></i> Compliant
                                </span>
                            @else
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Non-Compliant
                                </span>
                            @endif
                        </div>

                        @if(!empty($complianceStatus['items']))
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Name</th>
                                            <th>Expiry Date</th>
                                            <th>Status</th>
                                            <th>Document</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($complianceStatus['items'] as $item)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-light text-dark">{{ $item['type'] ?? '—' }}</span>
                                                </td>
                                                <td>{{ $item['name'] ?? '—' }}</td>
                                                <td>
                                                    @if(!empty($item['expiry_date']))
                                                        @php
                                                            $expiryDate = \Carbon\Carbon::parse($item['expiry_date']);
                                                            $isExpired = $expiryDate->isPast();
                                                            $isExpiringSoon = !$isExpired && $expiryDate->diffInDays(now()) <= 30;
                                                        @endphp
                                                        <span class="{{ $isExpired ? 'text-danger' : ($isExpiringSoon ? 'text-warning' : '') }}">
                                                            {{ $expiryDate->format('m/d/Y') }}
                                                            @if($isExpired)
                                                                <i class="fas fa-exclamation-circle ms-1" title="Expired"></i>
                                                            @elseif($isExpiringSoon)
                                                                <i class="fas fa-clock ms-1" title="Expiring soon"></i>
                                                            @endif
                                                        </span>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(($item['status'] ?? '') === 'valid')
                                                        <span class="badge bg-success">Valid</span>
                                                    @elseif(($item['status'] ?? '') === 'expired')
                                                        <span class="badge bg-danger">Expired</span>
                                                    @elseif(($item['status'] ?? '') === 'expiring_soon')
                                                        <span class="badge bg-warning text-dark">Expiring Soon</span>
                                                    @elseif(($item['status'] ?? '') === 'missing')
                                                        <span class="badge bg-secondary">Missing</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($item['status'] ?? 'Unknown') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!empty($item['document_id']))
                                                        <a href="{{ route('admin.contracts.download-document', [$contract->id, $item['document_id']]) }}"
                                                           class="btn btn-sm btn-outline-primary" title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-1"></i> No compliance items defined for this contract.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-circle me-1"></i> Compliance data is not available for this contract.
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>{{-- end tab-content --}}

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Restore active tab from URL hash
    var hash = window.location.hash;
    if (hash) {
        var tab = document.querySelector('#contractTabs button[data-bs-target="' + hash + '"]');
        if (tab) {
            var bsTab = new bootstrap.Tab(tab);
            bsTab.show();
        }
    }

    // Update URL hash when switching tabs
    $('#contractTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        var target = $(e.target).attr('data-bs-target');
        if (history.replaceState) {
            history.replaceState(null, null, target);
        } else {
            window.location.hash = target;
        }
    });
});
</script>
@endpush
