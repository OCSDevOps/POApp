@extends('layouts.admin')

@section('title', 'AI Settings')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-robot"></i> AI Settings
        </h1>
    </div>

    <div class="row">
        {{-- Card 1: AI Integration Settings --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-robot me-1"></i> AI Integration Settings
                    </h6>
                </div>
                <div class="card-body">
                    @include('partials.validation-errors')

                    <form method="POST" action="{{ route('admin.ai-settings.update') }}">
                        @csrf

                        {{-- Provider (read-only) --}}
                        <div class="mb-3">
                            <label class="form-label">Provider</label>
                            <div class="form-control bg-light">
                                <i class="fas fa-brain me-1 text-primary"></i> OpenAI GPT-4 Vision
                            </div>
                        </div>

                        {{-- API Key --}}
                        <div class="mb-3">
                            <label for="api_key" class="form-label">
                                @if($settings && $settings->api_key)
                                    Update API Key
                                @else
                                    API Key <span class="text-danger">*</span>
                                @endif
                            </label>
                            @if($settings && $settings->api_key)
                                <div class="mb-2">
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Configured</span>
                                    <code class="ms-2">{{ $settings->masked_api_key }}</code>
                                </div>
                            @endif
                            <input type="password"
                                   name="api_key"
                                   id="api_key"
                                   class="form-control"
                                   placeholder="{{ $settings && $settings->api_key ? 'Leave blank to keep current key' : 'Enter your OpenAI API key' }}">
                            <small class="form-text text-muted">
                                Get your API key from <a href="https://platform.openai.com" target="_blank">platform.openai.com</a>
                            </small>
                        </div>

                        {{-- Model --}}
                        <div class="mb-3">
                            <label for="model_name" class="form-label">Model</label>
                            <select name="model_name" id="model_name" class="form-select">
                                <option value="gpt-4o"
                                    @selected(old('model_name', $settings->model_name ?? 'gpt-4o') === 'gpt-4o')>
                                    gpt-4o (recommended)
                                </option>
                                <option value="gpt-4o-mini"
                                    @selected(old('model_name', $settings->model_name ?? 'gpt-4o') === 'gpt-4o-mini')>
                                    gpt-4o-mini (faster, cheaper)
                                </option>
                            </select>
                        </div>

                        {{-- Max Tokens --}}
                        <div class="mb-3">
                            <label for="max_tokens" class="form-label">Max Tokens</label>
                            <input type="number"
                                   name="max_tokens"
                                   id="max_tokens"
                                   class="form-control"
                                   min="1000"
                                   max="16000"
                                   value="{{ old('max_tokens', $settings->max_tokens ?? 4096) }}">
                        </div>

                        {{-- Temperature --}}
                        <div class="mb-3">
                            <label for="temperature" class="form-label">
                                Temperature: <span id="temperatureValue">{{ old('temperature', $settings->temperature ?? 0.2) }}</span>
                            </label>
                            <input type="range"
                                   name="temperature"
                                   id="temperature"
                                   class="form-range"
                                   min="0"
                                   max="1"
                                   step="0.1"
                                   value="{{ old('temperature', $settings->temperature ?? 0.2) }}">
                            <small class="form-text text-muted">
                                Lower = more precise, Higher = more creative
                            </small>
                        </div>

                        {{-- Active --}}
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       role="switch"
                                       name="is_active"
                                       id="is_active"
                                       value="1"
                                       {{ $settings && $settings->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <hr>

                        {{-- Buttons --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" id="testBtn" class="btn btn-outline-info">
                                    <i class="fas fa-satellite-dish me-1"></i> Test Connection
                                </button>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Settings
                                </button>
                            </div>
                        </div>

                        {{-- Test Result --}}
                        <div id="testResult" class="mt-3" style="display: none;"></div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Card 2: How It Works --}}
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-1"></i> How It Works
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li class="mb-2">Configure your OpenAI API key above</li>
                        <li class="mb-2">Create a Takeoff and upload construction drawings (PDF or images)</li>
                        <li class="mb-2">Click "Process with AI" on each drawing</li>
                        <li class="mb-2">AI will analyze the drawing and extract materials, quantities, and units</li>
                        <li class="mb-2">Review and edit the extracted items, then finalize the takeoff</li>
                        <li>Optionally convert the takeoff into a Purchase Order</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Temperature slider: update display value on input
        $('#temperature').on('input', function() {
            $('#temperatureValue').text($(this).val());
        });

        // Test Connection
        $('#testBtn').on('click', function() {
            var btn = $(this);
            var resultDiv = $('#testResult');

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Testing...');
            resultDiv.hide();

            $.ajax({
                url: '{{ route("admin.ai-settings.test") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.success) {
                        resultDiv.html(
                            '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">' +
                                '<i class="fas fa-check-circle me-1"></i> ' + (res.message || 'Connection successful!') +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>'
                        ).show();
                    } else {
                        resultDiv.html(
                            '<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">' +
                                '<i class="fas fa-times-circle me-1"></i> ' + (res.message || 'Connection failed.') +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>'
                        ).show();
                    }
                },
                error: function(xhr) {
                    var message = 'Connection test failed.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    resultDiv.html(
                        '<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">' +
                            '<i class="fas fa-times-circle me-1"></i> ' + message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>'
                    ).show();
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-satellite-dish me-1"></i> Test Connection');
                }
            });
        });
    });
</script>
@endpush
