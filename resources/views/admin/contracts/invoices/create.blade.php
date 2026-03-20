@extends('layouts.admin')

@section('title', 'Create Invoice - ' . $contract->contract_number)

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
                    <li class="breadcrumb-item active">New Invoice</li>
                </ol>
            </nav>

            <!-- Contract Info -->
            <div class="card mb-4">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-2">
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
                        <div class="col-md-2">
                            <small class="text-muted d-block">Retention %</small>
                            <strong>{{ $contract->retention_percentage ?? 0 }}%</strong>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted d-block">Remaining to Invoice</small>
                            <strong class="text-primary">${{ number_format($contract->remaining_to_invoice ?? 0, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Invoice Form -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Create Invoice for {{ $contract->contract_number }}</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.contracts.invoices.store', $contract->contract_id) }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="gross_amount" class="form-label">Gross Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" name="gross_amount" id="gross_amount"
                                           class="form-control @error('gross_amount') is-invalid @enderror"
                                           value="{{ old('gross_amount') }}" required
                                           placeholder="0.00">
                                    @error('gross_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="invoice_date" class="form-label">Invoice Date <span class="text-danger">*</span></label>
                                <input type="date" name="invoice_date" id="invoice_date"
                                       class="form-control @error('invoice_date') is-invalid @enderror"
                                       value="{{ old('invoice_date', date('Y-m-d')) }}" required>
                                @error('invoice_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" rows="3"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Invoice description...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="due_date"
                                       class="form-control @error('due_date') is-invalid @enderror"
                                       value="{{ old('due_date') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="period_from" class="form-label">Period From</label>
                                <input type="date" name="period_from" id="period_from"
                                       class="form-control @error('period_from') is-invalid @enderror"
                                       value="{{ old('period_from') }}">
                                @error('period_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="period_to" class="form-label">Period To</label>
                                <input type="date" name="period_to" id="period_to"
                                       class="form-control @error('period_to') is-invalid @enderror"
                                       value="{{ old('period_to') }}">
                                @error('period_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Retention Info Box -->
                        <div class="alert alert-info mb-4" id="retentionInfo">
                            <i class="fas fa-info-circle"></i>
                            Retention of <strong>{{ $contract->retention_percentage ?? 0 }}%</strong>
                            (<strong>$<span id="retentionAmount">0.00</span></strong>)
                            will be automatically held from this invoice.
                            Net payable: <strong>$<span id="netPayable">0.00</span></strong>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.contracts.invoices.index', $contract->contract_id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Invoices
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var retentionPct = {{ $contract->retention_percentage ?? 0 }};

    function updateRetentionCalc() {
        var gross = parseFloat($('#gross_amount').val()) || 0;
        var retention = gross * (retentionPct / 100);
        var net = gross - retention;

        $('#retentionAmount').text(retention.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
        $('#netPayable').text(net.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
    }

    $('#gross_amount').on('input keyup change', function() {
        updateRetentionCalc();
    });

    // Calculate on page load if there's a pre-filled value
    updateRetentionCalc();
});
</script>
@endpush
