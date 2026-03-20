@extends('layouts.admin')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.contracts.index') }}">Contracts</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.contracts.show', $contract->contract_id) }}">{{ $contract->contract_number }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.contracts.invoices.index', $contract->contract_id) }}">Invoices</a></li>
                    <li class="breadcrumb-item active">{{ $invoice->invoice_number }}</li>
                </ol>
            </nav>

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

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="fas fa-file-invoice-dollar"></i> {{ $invoice->invoice_number }}
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
                </h4>
                <a href="{{ route('admin.contracts.invoices.index', $contract->contract_id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Invoices
                </a>
            </div>

            <!-- Financial Summary Cards -->
            <div class="row mb-4">
                <div class="col-md">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body py-3">
                            <small class="text-muted d-block">Gross Amount</small>
                            <h4 class="mb-0 text-primary">${{ number_format($invoice->gross_amount, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body py-3">
                            <small class="text-muted d-block">Retention Held</small>
                            <h4 class="mb-0 text-warning">${{ number_format($invoice->retention_held, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body py-3">
                            <small class="text-muted d-block">Net Amount</small>
                            <h4 class="mb-0 text-info">${{ number_format($invoice->net_amount, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body py-3">
                            <small class="text-muted d-block">Paid</small>
                            <h4 class="mb-0 text-success">${{ number_format($invoice->paid_amount, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card border-0 shadow-sm text-center">
                        <div class="card-body py-3">
                            <small class="text-muted d-block">Balance Due</small>
                            @php $balanceDue = $invoice->net_amount - $invoice->paid_amount; @endphp
                            <h4 class="mb-0 {{ $balanceDue > 0 ? 'text-danger' : 'text-success' }}">${{ number_format($balanceDue, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Invoice Details -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Invoice Details</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Invoice Date:</th>
                                    <td>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('m/d/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Due Date:</th>
                                    <td>{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('m/d/Y') : 'N/A' }}</td>
                                </tr>
                                @if($invoice->period_from || $invoice->period_to)
                                    <tr>
                                        <th>Period:</th>
                                        <td>
                                            {{ $invoice->period_from ? \Carbon\Carbon::parse($invoice->period_from)->format('m/d/Y') : 'N/A' }}
                                            &mdash;
                                            {{ $invoice->period_to ? \Carbon\Carbon::parse($invoice->period_to)->format('m/d/Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $invoice->description ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td><span class="badge {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                                </tr>
                                <tr>
                                    <th>Created By:</th>
                                    <td>{{ $invoice->creator->name ?? $invoice->creator->user_name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="col-md-4">
                    @if(in_array($invoice->invoice_status, [3, 5]))
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-money-check-alt"></i> Record Payment</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.contracts.invoices.pay', [$contract->contract_id, $invoice->invoice_id]) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="payment_amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" step="0.01" min="0.01"
                                                   max="{{ $balanceDue }}" name="amount" id="payment_amount"
                                                   class="form-control @error('amount') is-invalid @enderror"
                                                   value="{{ old('amount') }}" required
                                                   placeholder="0.00">
                                            @error('amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <small class="text-muted">Balance due: ${{ number_format($balanceDue, 2) }}</small>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-credit-card"></i> Record Payment
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Contract Info Sidebar -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Contract Info</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <th>Contract #:</th>
                                    <td>
                                        <a href="{{ route('admin.contracts.show', $contract->contract_id) }}">
                                            {{ $contract->contract_number }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Project:</th>
                                    <td>{{ $contract->project->proj_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Subcontractor:</th>
                                    <td>{{ $contract->supplier->sup_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Retention:</th>
                                    <td>{{ $contract->retention_percentage ?? 0 }}%</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
