@extends('layouts.admin')

@section('title', 'Compliance: ' . $supplier->sup_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                Compliance: {{ $supplier->sup_name }}
                @if($status['is_compliant'])
                    <span class="badge bg-success ms-2">Compliant</span>
                @else
                    <span class="badge bg-danger ms-2">Non-Compliant</span>
                @endif
            </h4>
            <p class="text-muted mb-0">Manage compliance items for this supplier.</p>
        </div>
        <div>
            <a href="{{ route('admin.compliance.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
            <button class="btn btn-primary" id="btnAddCompliance" data-bs-toggle="modal" data-bs-target="#complianceModal">
                <i class="fas fa-plus me-1"></i> Add Compliance Item
            </button>
        </div>
    </div>

    {{-- Missing Required Items Alert --}}
    @if(!empty($status['missing_required']) && count($status['missing_required']) > 0)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Missing Required Compliance Items</h6>
            <ul class="mb-0">
                @foreach($status['missing_required'] as $missing)
                    <li>{{ $missing }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Compliance Items Table --}}
    <div class="card">
        <div class="card-header">Compliance Items</div>
        <div class="card-body table-responsive">
            <table class="table table-striped datatable align-middle">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Policy/License #</th>
                        <th>Issuer</th>
                        <th class="text-end">Coverage Amount</th>
                        <th>Issue Date</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th>Document</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($status['items'] as $item)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $item->type_text }}</span>
                            </td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->policy_number ?? '-' }}</td>
                            <td>{{ $item->issuer ?? '-' }}</td>
                            <td class="text-end">
                                @if($item->coverage_amount)
                                    ${{ number_format($item->coverage_amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->issue_date ? \Carbon\Carbon::parse($item->issue_date)->format('m/d/Y') : '-' }}</td>
                            <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('m/d/Y') : '-' }}</td>
                            <td>
                                @if($item->status === 'expired')
                                    <span class="badge bg-danger">Expired</span>
                                @elseif($item->status === 'expiring_soon')
                                    <span class="badge bg-warning text-dark">Expiring Soon</span>
                                @else
                                    <span class="badge bg-success">Current</span>
                                @endif
                            </td>
                            <td>
                                @if($item->document_path)
                                    <a href="{{ asset('storage/' . $item->document_path) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Download Document">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary me-1 edit-compliance-btn"
                                        data-id="{{ $item->id }}"
                                        data-type="{{ $item->type }}"
                                        data-name="{{ $item->name }}"
                                        data-policy-number="{{ $item->policy_number }}"
                                        data-issuer="{{ $item->issuer }}"
                                        data-coverage-amount="{{ $item->coverage_amount }}"
                                        data-issue-date="{{ $item->issue_date }}"
                                        data-expiry-date="{{ $item->expiry_date }}"
                                        data-warning-days="{{ $item->warning_days }}"
                                        data-is-required="{{ $item->is_required ? '1' : '0' }}"
                                        data-notes="{{ $item->notes }}"
                                        title="Edit">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <form action="{{ route('admin.supplier-compliance.destroy', [$supplier->sup_id, $item->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this compliance item?')" title="Delete">
                                        <i class="fas fa-trash"></i>
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

{{-- Add/Edit Compliance Modal --}}
<div class="modal fade" id="complianceModal" tabindex="-1" aria-labelledby="complianceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="complianceForm" method="POST" action="{{ route('admin.supplier-compliance.store', $supplier->sup_id) }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="complianceMethodField" name="_method" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="complianceModalLabel">Add Compliance Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="comp_type" class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" id="comp_type" class="form-select" required>
                                <option value="">-- Select Type --</option>
                                @foreach($typeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="comp_name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="comp_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="comp_policy_number" class="form-label">Policy/License #</label>
                            <input type="text" name="policy_number" id="comp_policy_number" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="comp_issuer" class="form-label">Issuer</label>
                            <input type="text" name="issuer" id="comp_issuer" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="comp_coverage_amount" class="form-label">Coverage Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="coverage_amount" id="comp_coverage_amount" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="comp_warning_days" class="form-label">Warning Days</label>
                            <input type="number" name="warning_days" id="comp_warning_days" class="form-control" value="30" min="1">
                        </div>
                        <div class="col-md-6">
                            <label for="comp_issue_date" class="form-label">Issue Date</label>
                            <input type="date" name="issue_date" id="comp_issue_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="comp_expiry_date" class="form-label">Expiry Date</label>
                            <input type="date" name="expiry_date" id="comp_expiry_date" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_required" id="comp_is_required" class="form-check-input" value="1">
                                <label for="comp_is_required" class="form-check-label">Required for compliance</label>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="comp_notes" class="form-label">Notes</label>
                            <textarea name="notes" id="comp_notes" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label for="comp_document" class="form-label">Document</label>
                            <input type="file" name="document" id="comp_document" class="form-control">
                            <div id="comp_existing_document" class="form-text d-none">
                                <i class="fas fa-paperclip me-1"></i>
                                <span id="comp_existing_document_text"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="complianceSubmitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        var storeUrl = "{{ route('admin.supplier-compliance.store', $supplier->sup_id) }}";
        var updateUrlTemplate = "{{ route('admin.supplier-compliance.update', [$supplier->sup_id, ':id']) }}";

        // Reset modal to Add mode when opening via the Add button
        $('#btnAddCompliance').on('click', function () {
            resetComplianceModal();
        });

        // Also reset when modal is hidden
        $('#complianceModal').on('hidden.bs.modal', function () {
            resetComplianceModal();
        });

        // Edit button handler
        $('.edit-compliance-btn').on('click', function () {
            var btn = $(this);
            var itemId = btn.data('id');

            // Set modal to edit mode
            $('#complianceModalLabel').text('Edit Compliance Item');
            $('#complianceSubmitBtn').text('Update');
            $('#complianceMethodField').val('PUT');
            $('#complianceForm').attr('action', updateUrlTemplate.replace(':id', itemId));

            // Populate fields
            $('#comp_type').val(btn.data('type'));
            $('#comp_name').val(btn.data('name'));
            $('#comp_policy_number').val(btn.data('policy-number'));
            $('#comp_issuer').val(btn.data('issuer'));
            $('#comp_coverage_amount').val(btn.data('coverage-amount'));
            $('#comp_issue_date').val(btn.data('issue-date'));
            $('#comp_expiry_date').val(btn.data('expiry-date'));
            $('#comp_warning_days').val(btn.data('warning-days') || 30);
            $('#comp_notes').val(btn.data('notes'));

            if (btn.data('is-required') == '1') {
                $('#comp_is_required').prop('checked', true);
            } else {
                $('#comp_is_required').prop('checked', false);
            }

            // Show modal
            $('#complianceModal').modal('show');
        });

        function resetComplianceModal() {
            $('#complianceModalLabel').text('Add Compliance Item');
            $('#complianceSubmitBtn').text('Save');
            $('#complianceMethodField').val('POST');
            $('#complianceForm').attr('action', storeUrl);
            $('#complianceForm')[0].reset();
            $('#comp_warning_days').val(30);
            $('#comp_existing_document').addClass('d-none');
        }
    });
</script>
@endpush
