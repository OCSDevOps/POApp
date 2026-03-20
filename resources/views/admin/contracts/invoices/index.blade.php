@extends('layouts.admin')

@section('title', 'Invoices - ' . $contract->contract_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.contracts.index') }}">Contracts</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.contracts.show', $contract->contract_id) }}">{{ $contract->contract_number }}</a></li>
                    <li class="breadcrumb-item active">Invoices</li>
                </ol>
            </nav>

            <!-- Contract Info Bar -->
            <div class="card mb-4">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <small class="text-muted d-block">Contract #</small>
                            <strong>{{ $contract->contract_number }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Project</small>
                            <strong>{{ $contract->project->proj_name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Subcontractor</small>
                            <strong>{{ $contract->supplier->sup_name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Retention %</small>
                            <strong>{{ $contract->retention_percentage ?? 0 }}%</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoices Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Invoices for {{ $contract->contract_number }}</h4>
                    <a href="{{ route('admin.contracts.invoices.create', $contract->contract_id) }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> New Invoice
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($invoices->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No invoices found for this contract.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover datatable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th>Gross Amount</th>
                                        <th>Retention Held</th>
                                        <th>Net Amount</th>
                                        <th>Paid</th>
                                        <th>Balance Due</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                        <tr>
                                            <td><strong>{{ $invoice->invoice_number }}</strong></td>
                                            <td>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('m/d/Y') : 'N/A' }}</td>
                                            <td class="text-end">${{ number_format($invoice->gross_amount, 2) }}</td>
                                            <td class="text-end">${{ number_format($invoice->retention_held, 2) }}</td>
                                            <td class="text-end">${{ number_format($invoice->net_amount, 2) }}</td>
                                            <td class="text-end">${{ number_format($invoice->paid_amount, 2) }}</td>
                                            <td class="text-end">
                                                <strong>${{ number_format($invoice->net_amount - $invoice->paid_amount, 2) }}</strong>
                                            </td>
                                            <td>
                                                @php
                                                    $statusLabels = [
                                                        0 => ['label' => 'Cancelled', 'class' => 'bg-dark'],
                                                        1 => ['label' => 'Draft', 'class' => 'bg-secondary'],
                                                        2 => ['label' => 'Submitted', 'class' => 'bg-info'],
                                                        3 => ['label' => 'Approved', 'class' => 'bg-primary'],
                                                        4 => ['label' => 'Paid', 'class' => 'bg-success'],
                                                        5 => ['label' => 'Partially Paid', 'class' => 'bg-warning'],
                                                        6 => ['label' => 'Rejected', 'class' => 'bg-danger'],
                                                    ];
                                                    $status = $statusLabels[$invoice->invoice_status] ?? ['label' => 'Unknown', 'class' => 'bg-secondary'];
                                                @endphp
                                                <span class="badge {{ $status['class'] }}">{{ $status['label'] }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.contracts.invoices.show', [$contract->contract_id, $invoice->invoice_id]) }}"
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
