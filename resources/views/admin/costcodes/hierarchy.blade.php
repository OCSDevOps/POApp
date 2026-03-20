@extends('layouts.admin')

@section('title', 'Cost Code Hierarchy')

@push('styles')
<style>
.hierarchy-tree {
    padding-left: 0;
    list-style: none;
}

.hierarchy-tree ul {
    padding-left: 30px;
    list-style: none;
}

.hierarchy-item {
    padding: 10px;
    margin: 5px 0;
    border-left: 3px solid #dee2e6;
    background: #f8f9fa;
    border-radius: 4px;
    transition: all 0.2s;
}

.hierarchy-item:hover {
    border-left-color: #0d6efd;
    background: #e7f1ff;
}

.hierarchy-item.level-1 {
    border-left-color: #0d6efd;
    background: #cfe2ff;
}

.hierarchy-item.level-2 {
    border-left-color: #0dcaf0;
    background: #e7f6f8;
}

.hierarchy-item.level-3 {
    border-left-color: #198754;
    background: #d1e7dd;
}

.code-badge {
    font-family: 'Courier New', monospace;
    font-weight: bold;
    padding: 4px 8px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}

.toggle-children {
    cursor: pointer;
    transition: transform 0.2s;
}

.toggle-children.collapsed {
    transform: rotate(-90deg);
}

.code-form-wrapper {
    position: sticky;
    top: 20px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1"><i class="fas fa-sitemap"></i> Cost Code Hierarchy</h1>
                    <p class="text-muted mb-0">Manage hierarchical cost code structure (XX-XX-XX format)</p>
                </div>
                <a href="{{ route('admin.costcodes.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list"></i> Flat View
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Hierarchy Tree -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-project-diagram"></i> Cost Code Structure</h5>
                </div>
                <div class="card-body">
                    @if(empty($hierarchy))
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No cost codes found. Create your first hierarchical cost code using the form.
                        </div>
                    @else
                        <ul class="hierarchy-tree">
                            @foreach($hierarchy as $node)
                                @include('admin.costcodes.partials.hierarchy-node', ['node' => $node])
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add Cost Code Form -->
        <div class="col-md-4">
            <div class="code-form-wrapper">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Add Cost Code</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.costcodes.store-hierarchical') }}" method="POST" id="hierarchyForm">
                            @csrf

                            <div class="mb-3">
                                <label for="category_code" class="form-label">Category Code <span class="text-danger">*</span></label>
                                <input type="text" name="category_code" id="category_code" 
                                       class="form-control" maxlength="10" required
                                       placeholder="e.g., 01, 02, 03">
                                <small class="text-muted">2-digit category identifier</small>
                            </div>

                            <div class="mb-3">
                                <label for="subcategory_code" class="form-label">Subcategory Code</label>
                                <input type="text" name="subcategory_code" id="subcategory_code" 
                                       class="form-control" maxlength="10"
                                       placeholder="e.g., 10, 20, 30">
                                <small class="text-muted">Optional: 2-digit subcategory (creates XX-XX)</small>
                            </div>

                            <div class="mb-3">
                                <label for="detail_code" class="form-label">Detail Code</label>
                                <input type="text" name="detail_code" id="detail_code" 
                                       class="form-control" maxlength="10"
                                       placeholder="e.g., 01, 02, 03">
                                <small class="text-muted">Optional: 2-digit detail (creates XX-XX-XX)</small>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <input type="text" name="description" id="description" 
                                       class="form-control" maxlength="500" required
                                       placeholder="e.g., General Conditions">
                            </div>

                            <div class="mb-3">
                                <label for="parent_code" class="form-label">Parent Code</label>
                                <select name="parent_code" id="parent_code" class="form-select">
                                    <option value="">None (Root Level)</option>
                                    @foreach($rootCodes as $code)
                                        <option value="{{ $code->full_code }}">{{ $code->full_code }} - {{ $code->cc_description }}</option>
                                        @foreach($code->children as $child)
                                            <option value="{{ $child->full_code }}">&nbsp;&nbsp;{{ $child->full_code }} - {{ $child->cc_description }}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                                <small class="text-muted">Optional: Select parent for nesting</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Preview</label>
                                <div class="p-2 bg-light border rounded">
                                    <strong>Full Code:</strong> <span id="code_preview" class="code-badge">--</span><br>
                                    <strong>Level:</strong> <span id="level_preview">1 (Category)</span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save"></i> Add Cost Code
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Legend -->
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Structure Guide</h6>
                    </div>
                    <div class="card-body p-2">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td><span class="code-badge bg-primary text-white">01</span></td>
                                    <td>Category (Level 1)</td>
                                </tr>
                                <tr>
                                    <td><span class="code-badge bg-info text-white">01-10</span></td>
                                    <td>Subcategory (Level 2)</td>
                                </tr>
                                <tr>
                                    <td><span class="code-badge bg-success text-white">01-10-01</span></td>
                                    <td>Detail (Level 3)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Common Examples -->
                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Common Examples</h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="small">
                            <div class="mb-2">
                                <strong>01</strong> - General Conditions<br>
                                <strong>02</strong> - Site Construction<br>
                                <strong>03</strong> - Concrete<br>
                                <strong>04</strong> - Masonry<br>
                                <strong>05</strong> - Metals<br>
                                <strong>06</strong> - Wood & Plastics
                            </div>
                            <div class="mb-2">
                                <strong>03-10</strong> - Concrete Forming<br>
                                <strong>03-20</strong> - Concrete Reinforcing<br>
                                <strong>03-30</strong> - Cast-In-Place Concrete
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Live preview of full code
    function updatePreview() {
        const category = $('#category_code').val() || '--';
        const subcategory = $('#subcategory_code').val();
        const detail = $('#detail_code').val();

        let fullCode = category;
        let level = 1;
        let levelText = '1 (Category)';

        if (subcategory) {
            fullCode += '-' + subcategory;
            level = 2;
            levelText = '2 (Subcategory)';
        }

        if (detail) {
            fullCode += '-' + detail;
            level = 3;
            levelText = '3 (Detail)';
        }

        $('#code_preview').text(fullCode);
        $('#level_preview').text(levelText);

        // Update badge color
        $('#code_preview').removeClass('bg-primary bg-info bg-success text-white');
        if (level === 1) {
            $('#code_preview').addClass('bg-primary text-white');
        } else if (level === 2) {
            $('#code_preview').addClass('bg-info text-white');
        } else {
            $('#code_preview').addClass('bg-success text-white');
        }
    }

    $('#category_code, #subcategory_code, #detail_code').on('input', updatePreview);

    // Toggle hierarchy children
    $(document).on('click', '.toggle-children', function(e) {
        e.preventDefault();
        $(this).toggleClass('collapsed');
        $(this).closest('.hierarchy-item').next('ul').slideToggle(200);
    });

    // Format codes to uppercase
    $('#category_code, #subcategory_code, #detail_code').on('input', function() {
        this.value = this.value.toUpperCase();
    });

    // Validate subcategory requires category
    $('#subcategory_code').on('blur', function() {
        if ($(this).val() && !$('#category_code').val()) {
            alert('Please enter a category code first.');
            $(this).val('');
        }
    });

    // Validate detail requires subcategory
    $('#detail_code').on('blur', function() {
        if ($(this).val() && !$('#subcategory_code').val()) {
            alert('Please enter a subcategory code first.');
            $(this).val('');
        }
    });
});
</script>
@endpush
