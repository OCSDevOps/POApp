{{--
    Attachment Manager Component
    
    Usage:
    @include('components.attachments.manager', [
        'attachableType' => 'purchase_order',
        'attachableId' => $purchaseOrder->porder_id,
        'attachments' => $purchaseOrder->activeAttachments,
        'categories' => ['quote', 'contract', 'invoice', 'photo'],
        'maxFiles' => 10,
        'maxFileSize' => 10240, // KB
    ])
--}}

@php
$maxFiles = $maxFiles ?? 10;
$maxFileSize = $maxFileSize ?? 10240;
$categories = $categories ?? [];
$containerId = 'attachment-manager-' . uniqid();
@endphp

<div id="{{ $containerId }}" class="attachment-manager" 
     data-attachable-type="{{ $attachableType }}"
     data-attachable-id="{{ $attachableId }}"
     data-max-files="{{ $maxFiles }}"
     data-max-file-size="{{ $maxFileSize }}">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-paperclip mr-1"></i> Attachments
            <span class="badge badge-secondary attachment-count">{{ $attachments->count() ?? 0 }}</span>
        </h6>
        <button type="button" class="btn btn-sm btn-primary btn-upload-attachment">
            <i class="fas fa-plus mr-1"></i> Add File
        </button>
    </div>

    {{-- File Input (Hidden) --}}
    <input type="file" class="d-none file-input" 
           accept=".pdf,.jpg,.jpeg,.png,.gif,.doc,.docx,.xls,.xlsx,.txt,.csv,.zip"
           {{ $maxFiles > 1 ? 'multiple' : '' }}>

    {{-- Progress Bar (Hidden by default) --}}
    <div class="upload-progress mb-3 d-none">
        <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                 role="progressbar" style="width: 0%"></div>
        </div>
        <small class="text-muted upload-status">Uploading...</small>
    </div>

    {{-- Category Selection Modal --}}
    @if(count($categories) > 0)
    <div class="category-selection mb-3 d-none">
        <label class="small text-muted">Category (optional):</label>
        <select class="form-control form-control-sm attachment-category">
            <option value="">-- Select Category --</option>
            @foreach($categories as $category)
                <option value="{{ $category }}">{{ ucfirst($category) }}</option>
            @endforeach
        </select>
    </div>
    @endif

    {{-- Attachments List --}}
    <div class="attachments-list">
        @if(isset($attachments) && $attachments->count() > 0)
            @foreach($attachments as $attachment)
            <div class="attachment-item d-flex align-items-center p-2 mb-2 border rounded" 
                 data-attachment-id="{{ $attachment->id }}">
                <div class="attachment-icon mr-3">
                    <i class="fas {{ $attachment->getIconClass() }} fa-2x text-primary"></i>
                </div>
                <div class="attachment-info flex-grow-1 min-width-0">
                    <div class="d-flex align-items-center">
                        <span class="attachment-name text-truncate font-weight-medium" 
                              title="{{ $attachment->original_name }}">
                            {{ $attachment->original_name }}
                        </span>
                        @if($attachment->category)
                            <span class="badge badge-light ml-2">{{ ucfirst($attachment->category) }}</span>
                        @endif
                    </div>
                    <small class="text-muted">
                        {{ $attachment->getFileSizeFormatted() }} • 
                        {{ $attachment->uploaded_at?->format('M d, Y H:i') }}
                    </small>
                    @if($attachment->description)
                        <div class="small text-muted text-truncate">{{ $attachment->description }}</div>
                    @endif
                </div>
                <div class="attachment-actions ml-2">
                    <a href="{{ route('admin.attachments.download', $attachment->id) }}" 
                       class="btn btn-sm btn-outline-primary" 
                       title="Download"
                       target="_blank">
                        <i class="fas fa-download"></i>
                    </a>
                    @if($attachment->isImage())
                        <button type="button" class="btn btn-sm btn-outline-info btn-preview" 
                                title="Preview"
                                data-url="{{ $attachment->getUrl() }}"
                                data-name="{{ $attachment->original_name }}">
                            <i class="fas fa-eye"></i>
                        </button>
                    @endif
                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-attachment" 
                            title="Delete"
                            data-attachment-id="{{ $attachment->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            @endforeach
        @else
            <div class="no-attachments text-center py-4 text-muted">
                <i class="fas fa-paperclip fa-3x mb-2 opacity-25"></i>
                <p class="mb-0">No attachments yet</p>
                <small>Click "Add File" to upload</small>
            </div>
        @endif
    </div>

    {{-- Help Text --}}
    <small class="text-muted d-block mt-2">
        Max file size: {{ number_format($maxFileSize / 1024, 1) }} MB. 
        Allowed types: PDF, Images, Office documents, CSV, TXT, ZIP.
    </small>
</div>

{{-- Image Preview Modal --}}
<div class="modal fade" id="attachmentPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title preview-filename"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-0">
                <img src="" alt="Preview" class="img-fluid preview-image" style="max-height: 70vh;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary preview-download" download>
                    <i class="fas fa-download mr-1"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const container = document.getElementById('{{ $containerId }}');
    const attachableType = container.dataset.attachableType;
    const attachableId = container.dataset.attachableId;
    const maxFiles = parseInt(container.dataset.maxFiles);
    const maxFileSize = parseInt(container.dataset.maxFileSize) * 1024; // Convert to bytes
    
    const fileInput = container.querySelector('.file-input');
    const uploadBtn = container.querySelector('.btn-upload-attachment');
    const progressBar = container.querySelector('.upload-progress');
    const progressInner = progressBar.querySelector('.progress-bar');
    const attachmentsList = container.querySelector('.attachments-list');
    
    // Upload button click
    uploadBtn.addEventListener('click', () => {
        const currentCount = container.querySelectorAll('.attachment-item').length;
        if (currentCount >= maxFiles) {
            alert('Maximum ' + maxFiles + ' files allowed.');
            return;
        }
        fileInput.click();
    });
    
    // File selection
    fileInput.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        const currentCount = container.querySelectorAll('.attachment-item').length;
        
        if (currentCount + files.length > maxFiles) {
            alert('Maximum ' + maxFiles + ' files allowed. You can upload ' + (maxFiles - currentCount) + ' more.');
            return;
        }
        
        files.forEach(file => {
            if (file.size > maxFileSize) {
                alert('File "' + file.name + '" exceeds maximum size of ' + (maxFileSize / 1024 / 1024).toFixed(1) + ' MB.');
                return;
            }
            uploadFile(file);
        });
        
        fileInput.value = '';
    });
    
    // Upload file function
    function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('attachable_type', attachableType);
        formData.append('attachable_id', attachableId);
        
        const categorySelect = container.querySelector('.attachment-category');
        if (categorySelect) {
            formData.append('category', categorySelect.value);
        }
        
        // Show progress
        progressBar.classList.remove('d-none');
        uploadBtn.disabled = true;
        
        $.ajax({
            url: '{{ route('admin.attachments.upload') }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percent = (e.loaded / e.total) * 100;
                        progressInner.style.width = percent + '%';
                    }
                });
                return xhr;
            },
            success: (response) => {
                progressBar.classList.add('d-none');
                progressInner.style.width = '0%';
                uploadBtn.disabled = false;
                
                if (response.success) {
                    addAttachmentToList(response.attachment);
                    updateAttachmentCount();
                    
                    // Remove "no attachments" message
                    const noAttachments = attachmentsList.querySelector('.no-attachments');
                    if (noAttachments) {
                        noAttachments.remove();
                    }
                } else {
                    alert(response.error || 'Upload failed');
                }
            },
            error: (xhr) => {
                progressBar.classList.add('d-none');
                progressInner.style.width = '0%';
                uploadBtn.disabled = false;
                
                const response = xhr.responseJSON;
                alert(response?.error || response?.message || 'Upload failed');
            }
        });
    }
    
    // Add attachment to list
    function addAttachmentToList(attachment) {
        const html = `
            <div class="attachment-item d-flex align-items-center p-2 mb-2 border rounded" 
                 data-attachment-id="${attachment.id}">
                <div class="attachment-icon mr-3">
                    <i class="fas ${attachment.icon_class} fa-2x text-primary"></i>
                </div>
                <div class="attachment-info flex-grow-1 min-width-0">
                    <div class="d-flex align-items-center">
                        <span class="attachment-name text-truncate font-weight-medium" 
                              title="${attachment.original_name}">
                            ${attachment.original_name}
                        </span>
                        ${attachment.category ? `<span class="badge badge-light ml-2">${attachment.category}</span>` : ''}
                    </div>
                    <small class="text-muted">
                        ${attachment.file_size_formatted} • ${attachment.uploaded_at}
                    </small>
                </div>
                <div class="attachment-actions ml-2">
                    <a href="/admin/attachments/${attachment.id}/download" 
                       class="btn btn-sm btn-outline-primary" 
                       title="Download"
                       target="_blank">
                        <i class="fas fa-download"></i>
                    </a>
                    ${attachment.is_image ? `
                        <button type="button" class="btn btn-sm btn-outline-info btn-preview" 
                                title="Preview"
                                data-url="${attachment.url}"
                                data-name="${attachment.original_name}">
                            <i class="fas fa-eye"></i>
                        </button>
                    ` : ''}
                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-attachment" 
                            title="Delete"
                            data-attachment-id="${attachment.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        attachmentsList.insertBefore(tempDiv.firstElementChild, attachmentsList.firstChild);
    }
    
    // Update attachment count
    function updateAttachmentCount() {
        const count = container.querySelectorAll('.attachment-item').length;
        container.querySelector('.attachment-count').textContent = count;
    }
    
    // Delete attachment
    container.addEventListener('click', (e) => {
        const deleteBtn = e.target.closest('.btn-delete-attachment');
        if (!deleteBtn) return;
        
        if (!confirm('Are you sure you want to delete this attachment?')) {
            return;
        }
        
        const attachmentId = deleteBtn.dataset.attachmentId;
        const item = deleteBtn.closest('.attachment-item');
        
        $.ajax({
            url: '/admin/attachments/' + attachmentId,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: (response) => {
                if (response.success) {
                    item.remove();
                    updateAttachmentCount();
                    
                    // Show "no attachments" message if empty
                    if (container.querySelectorAll('.attachment-item').length === 0) {
                        attachmentsList.innerHTML = `
                            <div class="no-attachments text-center py-4 text-muted">
                                <i class="fas fa-paperclip fa-3x mb-2 opacity-25"></i>
                                <p class="mb-0">No attachments yet</p>
                                <small>Click "Add File" to upload</small>
                            </div>
                        `;
                    }
                } else {
                    alert(response.error || 'Delete failed');
                }
            },
            error: (xhr) => {
                const response = xhr.responseJSON;
                alert(response?.error || 'Delete failed');
            }
        });
    });
    
    // Preview image
    container.addEventListener('click', (e) => {
        const previewBtn = e.target.closest('.btn-preview');
        if (!previewBtn) return;
        
        const url = previewBtn.dataset.url;
        const name = previewBtn.dataset.name;
        
        $('#attachmentPreviewModal .preview-image').attr('src', url);
        $('#attachmentPreviewModal .preview-filename').text(name);
        $('#attachmentPreviewModal .preview-download').attr('href', url).attr('download', name);
        $('#attachmentPreviewModal').modal('show');
    });
})();
</script>
@endpush

@push('styles')
<style>
.attachment-manager .attachment-item {
    background-color: #f8f9fa;
    transition: background-color 0.2s;
}
.attachment-manager .attachment-item:hover {
    background-color: #e9ecef;
}
.attachment-manager .attachment-name {
    max-width: 200px;
}
.attachment-manager .min-width-0 {
    min-width: 0;
}
@media (max-width: 576px) {
    .attachment-manager .attachment-name {
        max-width: 120px;
    }
}
</style>
@endpush
