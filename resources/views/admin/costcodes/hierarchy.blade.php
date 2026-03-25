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
                    <p class="text-muted mb-0">Manage standard cost codes in the March 2020 `1-00-00 / 2-03-00 / 2-03-30` format.</p>
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

    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please fix the highlighted hierarchy values.</div>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-project-diagram"></i> Cost Code Structure</h5>
                </div>
                <div class="card-body">
                    @if(empty($hierarchy))
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No cost codes found. Create your first standard hierarchy code using the form.
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

        <div class="col-md-4">
            <div class="code-form-wrapper">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0" id="form_title"><i class="fas fa-plus-circle"></i> Add Cost Code</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.costcodes.store-hierarchical') }}" method="POST" id="hierarchyForm">
                            @csrf
                            <input type="hidden" name="_method" id="method_override" value="PUT" disabled>
                            <input type="hidden" id="create_action" value="{{ route('admin.costcodes.store-hierarchical') }}">
                            <input type="hidden" id="update_base" value="{{ url('/admincontrol/costcodes/hierarchical') }}">

                            <div class="mb-3">
                                <label for="level" class="form-label">Hierarchy Level <span class="text-danger">*</span></label>
                                <select name="level" id="level" class="form-select" required>
                                    <option value="1">Level 1: Section Header</option>
                                    <option value="2">Level 2: Category</option>
                                    <option value="3" selected>Level 3: Detail</option>
                                </select>
                                <small class="text-muted">Use the attached standard list as the reference for new sections, categories, and detail codes.</small>
                            </div>

                            <div class="row">
                                <div class="col-4 mb-3">
                                    <label for="segment_1" class="form-label">Segment 1 <span class="text-danger">*</span></label>
                                    <input type="text" name="segment_1" id="segment_1" class="form-control" maxlength="2" required placeholder="2">
                                </div>
                                <div class="col-4 mb-3">
                                    <label for="segment_2" class="form-label">Segment 2</label>
                                    <input type="text" name="segment_2" id="segment_2" class="form-control" maxlength="2" placeholder="03">
                                </div>
                                <div class="col-4 mb-3">
                                    <label for="segment_3" class="form-label">Segment 3</label>
                                    <input type="text" name="segment_3" id="segment_3" class="form-control" maxlength="2" placeholder="30">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <input type="text" name="description" id="description" class="form-control" maxlength="500" required placeholder="Concrete Supply">
                            </div>

                            <div class="mb-3">
                                <label for="cc_status" class="form-label">Status</label>
                                <select name="cc_status" id="cc_status" class="form-select">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Preview</label>
                                <div class="p-2 bg-light border rounded">
                                    <strong>Full Code:</strong> <span id="code_preview" class="code-badge">--</span><br>
                                    <strong>Level:</strong> <span id="level_preview">3 (Detail)</span>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="submit_button">
                                    <i class="fas fa-save"></i> Add Cost Code
                                </button>
                                <button type="button" class="btn btn-outline-secondary d-none" id="cancel_edit_button">
                                    Cancel Edit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Structure Guide</h6>
                    </div>
                    <div class="card-body p-2">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td><span class="code-badge bg-primary text-white">2-00-00</span></td>
                                    <td>Section header (Level 1)</td>
                                </tr>
                                <tr>
                                    <td><span class="code-badge bg-info text-white">2-03-00</span></td>
                                    <td>Category (Level 2)</td>
                                </tr>
                                <tr>
                                    <td><span class="code-badge bg-success text-white">2-03-30</span></td>
                                    <td>Detail (Level 3)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Standard Sections</h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="small">
                            @foreach($standardSections as $section)
                                <div><strong>{{ $section['code'] }}</strong> - {{ $section['description'] }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Standard Detail Examples</h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="small">
                            @foreach($standardExamples as $example)
                                <div><strong>{{ $example['code'] }}</strong> - {{ $example['description'] }}</div>
                            @endforeach
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
    const createAction = $('#create_action').val();
    const updateBase = $('#update_base').val();

    function levelText(level) {
        if (level === 1) {
            return '1 (Section Header)';
        }
        if (level === 2) {
            return '2 (Category)';
        }
        return '3 (Detail)';
    }

    function sanitizeSegment(value) {
        return (value || '').toString().toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 2);
    }

    function previewSegment(value, fallback) {
        const cleaned = sanitizeSegment(value);
        if (!cleaned) {
            return fallback;
        }
        return cleaned.length === 1 ? cleaned : cleaned.padStart(2, '0');
    }

    function setSegmentState(level) {
        const segment2 = $('#segment_2');
        const segment3 = $('#segment_3');

        segment2.prop('readonly', level === 1);
        segment3.prop('readonly', level !== 3);

        if (level === 1) {
            segment2.val('00');
            segment3.val('00');
        } else if (level === 2) {
            if (segment2.val() === '00') {
                segment2.val('');
            }
            segment3.val('00');
        } else {
            if (segment3.val() === '00') {
                segment3.val('');
            }
        }
    }

    function updatePreview() {
        const level = parseInt($('#level').val(), 10) || 3;
        const segment1 = sanitizeSegment($('#segment_1').val()) || '--';
        const segment2 = level >= 2 ? previewSegment($('#segment_2').val(), '--') : '00';
        const segment3 = level === 3 ? previewSegment($('#segment_3').val(), '--') : '00';

        $('#code_preview')
            .text(`${segment1}-${segment2}-${segment3}`)
            .removeClass('bg-primary bg-info bg-success text-white');
        $('#level_preview').text(levelText(level));

        if (level === 1) {
            $('#code_preview').addClass('bg-primary text-white');
        } else if (level === 2) {
            $('#code_preview').addClass('bg-info text-white');
        } else {
            $('#code_preview').addClass('bg-success text-white');
        }
    }

    function resetForm() {
        $('#hierarchyForm').attr('action', createAction);
        $('#method_override').prop('disabled', true);
        $('#form_title').html('<i class="fas fa-plus-circle"></i> Add Cost Code');
        $('#submit_button').html('<i class="fas fa-save"></i> Add Cost Code');
        $('#cancel_edit_button').addClass('d-none');
        $('#hierarchyForm')[0].reset();
        $('#level').val('3');
        $('#cc_status').val('1');
        setSegmentState(3);
        updatePreview();
    }

    $('#level').on('change', function() {
        setSegmentState(parseInt(this.value, 10) || 3);
        updatePreview();
    });

    $('#segment_1, #segment_2, #segment_3').on('input', function() {
        this.value = sanitizeSegment(this.value);
        updatePreview();
    });

    $(document).on('click', '.toggle-children', function(e) {
        e.preventDefault();
        $(this).toggleClass('collapsed');
        $(this).closest('.hierarchy-item').next('ul').slideToggle(200);
    });

    window.addChild = function(button) {
        const currentLevel = parseInt(button.dataset.level, 10);
        if (currentLevel >= 3) {
            alert('Detail cost codes are already the lowest level in this hierarchy.');
            return;
        }

        resetForm();

        const nextLevel = currentLevel + 1;
        $('#level').val(String(nextLevel));
        setSegmentState(nextLevel);
        $('#segment_1').val(button.dataset.segment1 || '');

        if (nextLevel === 3) {
            $('#segment_2').val(button.dataset.segment2 || '');
        }

        updatePreview();

        $(nextLevel === 2 ? '#segment_2' : '#segment_3').focus();
        $('html, body').animate({
            scrollTop: $('#hierarchyForm').offset().top - 100
        }, 500);
    };

    window.editCode = function(button) {
        $('#hierarchyForm').attr('action', `${updateBase}/${button.dataset.id}`);
        $('#method_override').prop('disabled', false);
        $('#form_title').html('<i class="fas fa-edit"></i> Edit Cost Code');
        $('#submit_button').html('<i class="fas fa-save"></i> Save Changes');
        $('#cancel_edit_button').removeClass('d-none');

        $('#level').val(button.dataset.level);
        $('#segment_1').val(button.dataset.segment1 || '');
        $('#segment_2').val(button.dataset.segment2 || '');
        $('#segment_3').val(button.dataset.segment3 || '');
        $('#description').val(button.dataset.description || '');
        $('#cc_status').val(button.dataset.status || '1');

        setSegmentState(parseInt(button.dataset.level, 10) || 3);
        updatePreview();

        $('html, body').animate({
            scrollTop: $('#hierarchyForm').offset().top - 100
        }, 500);
    };

    $('#cancel_edit_button').on('click', function() {
        resetForm();
    });

    resetForm();
});
</script>
@endpush
