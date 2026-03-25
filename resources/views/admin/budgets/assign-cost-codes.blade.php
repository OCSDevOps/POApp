@extends('layouts.admin')

@section('title', 'Assign Cost Codes - ' . $project->proj_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Assign Cost Codes to Project: {{ $project->proj_name }}</h4>
                    <a href="{{ route('admin.projects.show', $project->proj_id) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Project
                    </a>
                </div>

                <div class="card-body">
                    @include('partials.validation-errors')
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

                    <form method="POST" action="{{ route('admin.budgets.save-cost-codes', $project->proj_id) }}" id="costCodeForm">
                        @csrf

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Select cost codes to assign to this project. You can select entire sections, categories, or individual detail codes.
                        </div>

                        <div class="row g-3 align-items-end mb-3">
                            <div class="col-lg-7">
                                @if($templates->isNotEmpty())
                                    <label for="costCodeTemplate" class="form-label">Apply Seeded Tenant Template</label>
                                    <div class="input-group">
                                        <select class="form-select" id="costCodeTemplate">
                                            <option value="">Choose a reusable template...</option>
                                            @foreach($templates as $template)
                                                <option value="{{ $template->cct_id }}">
                                                    {{ $template->cct_name }} ({{ $template->items_count }} codes)
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-outline-primary" id="applyTemplate">
                                            <i class="fas fa-layer-group"></i> Apply Template
                                        </button>
                                    </div>
                                    <small class="text-muted">Includes the full March 2020 catalog plus section-based tenant template packs.</small>
                                @endif
                            </div>
                            <div class="col-lg-5">
                                <label class="form-label d-block">Quick Actions</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                                    <i class="fas fa-check-square"></i> Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">
                                    <i class="fas fa-square"></i> Deselect All
                                </button>
                            </div>
                        </div>

                        <div class="accordion" id="costCodeAccordion">
                            @foreach($parentCodes as $parent)
                                @php
                                    $categories = \App\Models\CostCode::byParent($parent->cc_parent_code)
                                        ->where('cc_level', 2)
                                        ->active()
                                        ->orderBy('cc_category_code')
                                        ->get();
                                @endphp

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $parent->cc_id }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $parent->cc_id }}" aria-expanded="false">
                                            <div class="form-check me-3" onclick="event.stopPropagation();">
                                                <input class="form-check-input parent-checkbox" type="checkbox" 
                                                       name="cost_code_ids[]"
                                                       value="{{ $parent->cc_id }}"
                                                       id="parent_{{ $parent->cc_id }}" 
                                                       data-parent="{{ $parent->cc_parent_code }}"
                                                       {{ in_array($parent->cc_id, $assignedCostCodeIds) ? 'checked' : '' }}>
                                            </div>
                                            <strong>{{ $parent->cc_full_code }}</strong> - {{ $parent->cc_description }}
                                            <span class="badge bg-secondary ms-2">{{ $categories->count() }} categories</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $parent->cc_id }}" class="accordion-collapse collapse" 
                                         data-bs-parent="#costCodeAccordion">
                                        <div class="accordion-body">
                                            @if($categories->isEmpty())
                                                <div class="text-muted">No categories defined</div>
                                            @else
                                                <div class="row">
                                                    @foreach($categories as $category)
                                                        @php
                                                            $subcategories = \App\Models\CostCode::byParent($parent->cc_parent_code)
                                                                ->where('cc_category_code', $category->cc_category_code)
                                                                ->where('cc_level', 3)
                                                                ->active()
                                                                ->orderBy('cc_subcategory_code')
                                                                ->get();
                                                        @endphp

                                                        <div class="col-md-6 mb-3">
                                                            <div class="card">
                                                                <div class="card-body">
                                                                    <div class="form-check mb-2">
                                                                        <input class="form-check-input category-checkbox" 
                                                                               type="checkbox" 
                                                                               name="cost_code_ids[]" 
                                                                               value="{{ $category->cc_id }}"
                                                                               id="category_{{ $category->cc_id }}"
                                                                               data-parent="{{ $parent->cc_parent_code }}"
                                                                               data-category="{{ $category->cc_category_code }}"
                                                                               {{ in_array($category->cc_id, $assignedCostCodeIds) ? 'checked' : '' }}>
                                                                        <label class="form-check-label fw-bold" for="category_{{ $category->cc_id }}">
                                                                            {{ $category->cc_full_code }} - {{ $category->cc_description }}
                                                                        </label>
                                                                    </div>

                                                                    @if($subcategories->isNotEmpty())
                                                                        <div class="ms-4">
                                                                            @foreach($subcategories as $sub)
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input subcategory-checkbox" 
                                                                                           type="checkbox" 
                                                                                           name="cost_code_ids[]" 
                                                                                           value="{{ $sub->cc_id }}"
                                                                                           id="sub_{{ $sub->cc_id }}"
                                                                                           data-parent="{{ $parent->cc_parent_code }}"
                                                                                           data-category="{{ $category->cc_category_code }}"
                                                                                           {{ in_array($sub->cc_id, $assignedCostCodeIds) ? 'checked' : '' }}>
                                                                                    <label class="form-check-label" for="sub_{{ $sub->cc_id }}">
                                                                                        {{ $sub->cc_full_code }} - {{ $sub->cc_description }}
                                                                                    </label>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Cost Code Assignments
                            </button>
                            <a href="{{ route('admin.budgets.setup', $project->proj_id) }}" class="btn btn-success">
                                <i class="fas fa-arrow-right"></i> Continue to Budget Setup
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const templateEndpoint = @json(route('admin.costcode-templates.cost-codes', ['id' => '__template__']));

    // Select all
    $('#selectAll').click(function() {
        $('.form-check-input').prop('checked', true);
    });

    // Deselect all
    $('#deselectAll').click(function() {
        $('.form-check-input').prop('checked', false);
    });

    // Parent checkbox logic
    $('.parent-checkbox').change(function() {
        const parent = $(this).data('parent');
        const checked = $(this).is(':checked');
        
        $(`.category-checkbox[data-parent="${parent}"], .subcategory-checkbox[data-parent="${parent}"]`)
            .prop('checked', checked);
    });

    // Category checkbox logic
    $('.category-checkbox').change(function() {
        const parent = $(this).data('parent');
        const category = $(this).data('category');
        const checked = $(this).is(':checked');
        
        // Check/uncheck subcategories
        $(`.subcategory-checkbox[data-parent="${parent}"][data-category="${category}"]`)
            .prop('checked', checked);
        
        // Update parent checkbox
        updateParentCheckbox(parent);
    });

    // Subcategory checkbox logic
    $('.subcategory-checkbox').change(function() {
        const parent = $(this).data('parent');
        const category = $(this).data('category');
        
        // Update category checkbox
        updateCategoryCheckbox(parent, category);
        updateParentCheckbox(parent);
    });

    function updateCategoryCheckbox(parent, category) {
        const totalSubs = $(`.subcategory-checkbox[data-parent="${parent}"][data-category="${category}"]`).length;
        const checkedSubs = $(`.subcategory-checkbox[data-parent="${parent}"][data-category="${category}"]:checked`).length;
        
        const categoryCheckbox = $(`.category-checkbox[data-parent="${parent}"][data-category="${category}"]`);

        if (totalSubs === 0) {
            return;
        }
        
        if (checkedSubs === 0) {
            categoryCheckbox.prop('checked', false);
        } else if (checkedSubs === totalSubs) {
            categoryCheckbox.prop('checked', true);
        }
    }

    function updateParentCheckbox(parent) {
        const totalCategories = $(`.category-checkbox[data-parent="${parent}"], .subcategory-checkbox[data-parent="${parent}"]`).length;
        const checkedCategories = $(`.category-checkbox[data-parent="${parent}"]:checked, .subcategory-checkbox[data-parent="${parent}"]:checked`).length;
        
        const parentCheckbox = $(`.parent-checkbox[data-parent="${parent}"]`);
        
        if (checkedCategories === 0) {
            parentCheckbox.prop('checked', false);
        } else if (checkedCategories === totalCategories) {
            parentCheckbox.prop('checked', true);
        }
    }

    $('#applyTemplate').click(function() {
        const templateId = $('#costCodeTemplate').val();

        if (!templateId) {
            return;
        }

        $.get(templateEndpoint.replace('__template__', templateId), function(items) {
            const selectedIds = new Set(items.map(item => String(item.cc_id)));

            $('.form-check-input').prop('checked', false);

            $('input[name="cost_code_ids[]"]').each(function() {
                $(this).prop('checked', selectedIds.has(String($(this).val())));
            });

            $('.category-checkbox').each(function() {
                updateCategoryCheckbox($(this).data('parent'), $(this).data('category'));
            });

            $('.parent-checkbox').each(function() {
                updateParentCheckbox($(this).data('parent'));
            });
        });
    });
});
</script>
@endpush
@endsection
