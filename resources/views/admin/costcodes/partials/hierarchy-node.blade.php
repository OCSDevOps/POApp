@php
    $code = $node['code'];
    $levelClass = 'level-' . ($code->cc_level ?? 1);
    $parts = array_pad(explode('-', $code->cc_full_code ?? $code->cc_no ?? ''), 3, '00');
    $segmentOne = $code->cc_parent_code ?? ($parts[0] ?? '');
    $segmentTwo = $code->cc_category_code ?? ($parts[1] ?? '00');
    $segmentThree = $code->cc_subcategory_code ?? ($parts[2] ?? '00');
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

                <span class="code-badge">{{ $code->cc_full_code ?? $code->cc_no }}</span>
                <span class="ms-2">{{ $code->cc_description }}</span>

                @if($code->cc_level)
                    <span class="badge bg-secondary ms-2">Level {{ $code->cc_level }}</span>
                @endif
            </div>

            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-sm btn-outline-primary"
                        data-id="{{ $code->cc_id }}"
                        data-level="{{ $code->cc_level }}"
                        data-segment1="{{ $segmentOne }}"
                        data-segment2="{{ $segmentTwo }}"
                        data-segment3="{{ $segmentThree }}"
                        data-description="{{ $code->cc_description }}"
                        data-status="{{ $code->cc_status }}"
                        onclick="editCode(this)" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-info"
                        data-level="{{ $code->cc_level }}"
                        data-segment1="{{ $segmentOne }}"
                        data-segment2="{{ $segmentTwo }}"
                        data-segment3="{{ $segmentThree }}"
                        onclick="addChild(this)" title="Add Child">
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
