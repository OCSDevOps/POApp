@extends('layouts.admin')

@section('title', 'Edit Template: ' . $template->cct_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0 font-weight-bold text-primary">
                <i class="fas fa-edit me-1"></i> Edit: {{ $template->cct_name }}
            </h5>
        </div>
        <a href="{{ route('admin.costcode-templates.show', $template->cct_id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.costcode-templates.update', $template->cct_id) }}" id="templateForm">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Template Info --}}
            <div class="col-md-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Template Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="cct_name" class="form-label">Template Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('cct_name') is-invalid @enderror"
                                   id="cct_name" name="cct_name"
                                   value="{{ old('cct_name', $template->cct_name) }}" required>
                            @error('cct_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="cct_description" class="form-label">Description</label>
                            <textarea class="form-control" id="cct_description" name="cct_description"
                                      rows="3">{{ old('cct_description', $template->cct_description) }}</textarea>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Selected: <strong id="selectedCount">0</strong> cost codes</span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-save me-1"></i> Update Template
                </button>
            </div>

            {{-- Cost Code Selection --}}
            <div class="col-md-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Select Cost Codes</h6>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">Select All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">Deselect All</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="searchCostCodes" placeholder="Search cost codes...">
                        </div>

                        <div style="max-height: 500px; overflow-y: auto;">
                            @foreach($parentCodes as $parent)
                                <div class="cost-code-group mb-2" data-search="{{ strtolower($parent->cc_full_code . ' ' . $parent->cc_description) }}">
                                    <div class="form-check border-bottom pb-2 mb-1">
                                        <input class="form-check-input cost-code-checkbox" type="checkbox"
                                               name="cost_code_ids[]" value="{{ $parent->cc_id }}"
                                               id="cc_{{ $parent->cc_id }}"
                                               {{ in_array($parent->cc_id, old('cost_code_ids', $selectedIds)) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold" for="cc_{{ $parent->cc_id }}">
                                            <span class="badge bg-dark me-1">{{ $parent->cc_full_code }}</span>
                                            {{ $parent->cc_description }}
                                        </label>
                                    </div>

                                    @foreach($categoryCodes->where('cc_parent_code', $parent->cc_no) as $category)
                                        <div class="ms-4 cost-code-item" data-search="{{ strtolower($category->cc_full_code . ' ' . $category->cc_description) }}">
                                            <div class="form-check mb-1">
                                                <input class="form-check-input cost-code-checkbox" type="checkbox"
                                                       name="cost_code_ids[]" value="{{ $category->cc_id }}"
                                                       id="cc_{{ $category->cc_id }}"
                                                       {{ in_array($category->cc_id, old('cost_code_ids', $selectedIds)) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cc_{{ $category->cc_id }}">
                                                    <span class="badge bg-info me-1">{{ $category->cc_full_code }}</span>
                                                    {{ $category->cc_description }}
                                                </label>
                                            </div>

                                            @foreach($subcategoryCodes->where('cc_parent_code', $parent->cc_no)->where('cc_category_code', $category->cc_category_code) as $sub)
                                                <div class="ms-4 cost-code-item" data-search="{{ strtolower($sub->cc_full_code . ' ' . $sub->cc_description) }}">
                                                    <div class="form-check mb-1">
                                                        <input class="form-check-input cost-code-checkbox" type="checkbox"
                                                               name="cost_code_ids[]" value="{{ $sub->cc_id }}"
                                                               id="cc_{{ $sub->cc_id }}"
                                                               {{ in_array($sub->cc_id, old('cost_code_ids', $selectedIds)) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="cc_{{ $sub->cc_id }}">
                                                            <span class="badge bg-secondary me-1">{{ $sub->cc_full_code }}</span>
                                                            {{ $sub->cc_description }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function updateCount() {
        $('#selectedCount').text($('.cost-code-checkbox:checked').length);
    }

    $('.cost-code-checkbox').on('change', function() {
        updateCount();
    });

    $('#selectAll').on('click', function() {
        $('.cost-code-checkbox:visible').prop('checked', true);
        updateCount();
    });

    $('#deselectAll').on('click', function() {
        $('.cost-code-checkbox').prop('checked', false);
        updateCount();
    });

    $('#searchCostCodes').on('keyup', function() {
        var q = $(this).val().toLowerCase();
        if (q === '') {
            $('.cost-code-group, .cost-code-item').show();
        } else {
            $('.cost-code-group').each(function() {
                var groupMatch = $(this).attr('data-search').indexOf(q) > -1;
                var childMatch = false;
                $(this).find('.cost-code-item').each(function() {
                    if ($(this).attr('data-search').indexOf(q) > -1) {
                        $(this).show();
                        childMatch = true;
                    } else {
                        $(this).hide();
                    }
                });
                $(this).toggle(groupMatch || childMatch);
            });
        }
    });

    updateCount();
});
</script>
@endpush
