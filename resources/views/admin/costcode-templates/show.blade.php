@extends('layouts.admin')

@section('title', 'Template: ' . $template->cct_name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0 font-weight-bold text-primary">
                <i class="fas fa-layer-group me-1"></i> {{ $template->cct_name }}
            </h5>
            <small class="text-muted">{{ $template->cct_description }}</small>
        </div>
        <div>
            <a href="{{ route('admin.costcode-templates.edit', $template->cct_id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.costcode-templates.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Template Info</h6>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Name</dt>
                        <dd>{{ $template->cct_name }}</dd>

                        <dt>Description</dt>
                        <dd>{{ $template->cct_description ?: '-' }}</dd>

                        <dt>Status</dt>
                        <dd>
                            @if($template->cct_status)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </dd>

                        <dt>Cost Codes</dt>
                        <dd><span class="badge bg-primary">{{ $items->count() }}</span></dd>

                        <dt>Created</dt>
                        <dd>{{ $template->cct_createdate ? $template->cct_createdate->format('m/d/Y h:i A') : '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cost Codes in Template</h6>
                </div>
                <div class="card-body">
                    @if($items->isEmpty())
                        <div class="alert alert-warning mb-0">No cost codes assigned to this template.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Code</th>
                                        <th>Description</th>
                                        <th>Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        @if($item->costCode)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @switch($item->costCode->cc_level)
                                                    @case(1)
                                                        <span class="badge bg-dark">{{ $item->costCode->cc_full_code }}</span>
                                                        @break
                                                    @case(2)
                                                        <span class="badge bg-info">{{ $item->costCode->cc_full_code }}</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $item->costCode->cc_full_code }}</span>
                                                @endswitch
                                            </td>
                                            <td>{{ $item->costCode->cc_description }}</td>
                                            <td>
                                                @switch($item->costCode->cc_level)
                                                    @case(1) Parent @break
                                                    @case(2) Category @break
                                                    @case(3) Subcategory @break
                                                    @default {{ $item->costCode->cc_level }}
                                                @endswitch
                                            </td>
                                        </tr>
                                        @endif
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
