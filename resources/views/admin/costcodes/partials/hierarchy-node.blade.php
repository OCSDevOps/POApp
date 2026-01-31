@php
    $code = $node['code'];
    $levelClass = 'level-' . ($code->level ?? 1);
@endphp

<li>
    <div class="hierarchy-item {{ $levelClass }}">
        <div class="d-flex justify-content-between align-items-center">
            <div class="flex-grow-1">
                @if(!empty($node['children']))
                    <i class="fas fa-chevron-down toggle-children me-2"></i>
                @else
                    <i class="fas fa-minus me-2 text-muted"></i>
                @endif
                
                <span class="code-badge">{{ $code->full_code ?? $code->cc_no }}</span>
                <span class="ms-2">{{ $code->cc_description }}</span>

                @if($code->level)
                    <span class="badge bg-secondary ms-2">Level {{ $code->level }}</span>
                @endif
            </div>

            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-sm btn-outline-primary" 
                        onclick="editCode({{ $code->cc_id }})" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-info" 
                        onclick="addChild('{{ $code->full_code ?? $code->cc_no }}')" title="Add Child">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </div>

    @if(!empty($node['children']))
        <ul class="hierarchy-children">
            @foreach($node['children'] as $childNode)
                @include('admin.costcodes.partials.hierarchy-node', ['node' => $childNode])
            @endforeach
        </ul>
    @endif
</li>

<script>
function addChild(parentCode) {
    $('#parent_code').val(parentCode);
    
    // Extract category and subcategory from parent
    const parts = parentCode.split('-');
    if (parts.length === 1) {
        // Parent is category, suggest subcategory
        $('#category_code').val(parts[0]);
        $('#subcategory_code').focus();
    } else if (parts.length === 2) {
        // Parent is subcategory, suggest detail
        $('#category_code').val(parts[0]);
        $('#subcategory_code').val(parts[1]);
        $('#detail_code').focus();
    }
    
    // Scroll to form
    $('html, body').animate({
        scrollTop: $('#hierarchyForm').offset().top - 100
    }, 500);
}

function editCode(codeId) {
    // TODO: Implement edit functionality
    alert('Edit functionality coming soon. Code ID: ' + codeId);
}
</script>
