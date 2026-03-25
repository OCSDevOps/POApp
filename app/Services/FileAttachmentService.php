<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FileAttachmentService
{
    /**
     * Allowed MIME types for upload.
     */
    protected array $allowedMimeTypes = [
        // Documents
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'text/csv',
        // Images
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/bmp',
        'image/webp',
        'image/svg+xml',
        // Archives
        'application/zip',
        'application/x-zip-compressed',
        'application/x-rar-compressed',
        'application/x-7z-compressed',
    ];

    /**
     * Allowed file extensions.
     */
    protected array $allowedExtensions = [
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv',
        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg',
        'zip', 'rar', '7z',
    ];

    /**
     * Maximum file size in KB (10MB default).
     */
    protected int $maxFileSize = 10240;

    /**
     * Default storage disk.
     */
    protected string $defaultDisk = 'public';

    /**
     * Upload and attach a single file to a model.
     */
    public function attachFile(
        Model $model,
        UploadedFile $file,
        ?string $category = null,
        ?string $description = null,
        ?string $disk = null
    ): array {
        try {
            // Validate file
            $validation = $this->validateFile($file);
            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'error' => $validation['error'],
                ];
            }

            $disk = $disk ?? $this->defaultDisk;
            $companyId = session('company_id');

            // Generate file hash for deduplication check
            $fileHash = hash_file('sha256', $file->getRealPath());

            // Check for existing identical file
            $existingAttachment = $this->findExistingFile($fileHash, $companyId);
            if ($existingAttachment && $existingAttachment->fileExists()) {
                // Reuse existing file but create new attachment record
                $storedPath = $existingAttachment->file_path;
                $storedName = $existingAttachment->stored_name;
            } else {
                // Store the file
                $folder = $this->getStorageFolder($model);
                $storedName = $this->generateStoredName($file);
                $storedPath = $file->storeAs($folder, $storedName, $disk);
            }

            // Create attachment record
            $attachment = Attachment::create([
                'company_id' => $companyId,
                'attachable_id' => $model->getKey(),
                'attachable_type' => get_class($model),
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'file_path' => $storedPath,
                'disk' => $disk,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'file_extension' => strtolower($file->getClientOriginalExtension()),
                'file_hash' => $fileHash,
                'category' => $category,
                'description' => $description,
                'sort_order' => $this->getNextSortOrder($model),
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
                'status' => 1,
            ]);

            return [
                'success' => true,
                'attachment' => $attachment,
                'url' => $attachment->getUrl(),
            ];

        } catch (\Exception $e) {
            \Log::error('File attachment failed', [
                'model' => get_class($model),
                'model_id' => $model->getKey(),
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to attach file: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Upload and attach multiple files to a model.
     */
    public function attachMultiple(
        Model $model,
        array $files,
        ?string $category = null,
        ?string $disk = null
    ): array {
        $results = [
            'success' => true,
            'attached' => [],
            'failed' => [],
        ];

        foreach ($files as $file) {
            $result = $this->attachFile($model, $file, $category, null, $disk);
            
            if ($result['success']) {
                $results['attached'][] = $result['attachment'];
            } else {
                $results['failed'][] = [
                    'name' => $file->getClientOriginalName(),
                    'error' => $result['error'],
                ];
            }
        }

        $results['success'] = count($results['failed']) === 0;
        
        return $results;
    }

    /**
     * Delete an attachment by ID.
     */
    public function deleteAttachment(int $attachmentId, bool $permanent = false): array
    {
        try {
            $attachment = Attachment::withoutGlobalScope(CompanyScope::class)->findOrFail($attachmentId);

            // Verify company ownership
            if ($attachment->company_id != session('company_id')) {
                return [
                    'success' => false,
                    'error' => 'Unauthorized access to attachment',
                ];
            }

            if ($permanent) {
                $attachment->permanentlyDelete();
            } else {
                $attachment->softDelete();
            }

            return [
                'success' => true,
                'message' => 'Attachment deleted successfully',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to delete attachment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Delete multiple attachments.
     */
    public function deleteAttachments(array $attachmentIds, bool $permanent = false): array
    {
        $results = [
            'success' => true,
            'deleted' => 0,
            'failed' => [],
        ];

        foreach ($attachmentIds as $id) {
            $result = $this->deleteAttachment($id, $permanent);
            
            if ($result['success']) {
                $results['deleted']++;
            } else {
                $results['failed'][] = ['id' => $id, 'error' => $result['error']];
            }
        }

        $results['success'] = count($results['failed']) === 0;
        
        return $results;
    }

    /**
     * Get all attachments for a model.
     */
    public function getAttachments(Model $model, ?string $category = null): array
    {
        $query = Attachment::where('attachable_id', $model->getKey())
            ->where('attachable_type', get_class($model))
            ->active()
            ->ordered();

        if ($category) {
            $query->byCategory($category);
        }

        return [
            'success' => true,
            'attachments' => $query->get(),
            'count' => $query->count(),
        ];
    }

    /**
     * Download an attachment.
     */
    public function downloadAttachment(int $attachmentId, bool $inline = false)
    {
        $attachment = Attachment::findOrFail($attachmentId);

        // Verify company ownership
        if ($attachment->company_id != session('company_id')) {
            abort(403, 'Unauthorized access');
        }

        if ($inline) {
            return $attachment->inlineResponse();
        }

        return $attachment->downloadResponse();
    }

    /**
     * Reorder attachments for a model.
     */
    public function reorderAttachments(Model $model, array $attachmentIds): array
    {
        try {
            foreach ($attachmentIds as $index => $id) {
                Attachment::where('id', $id)
                    ->where('company_id', session('company_id'))
                    ->update(['sort_order' => $index]);
            }

            return [
                'success' => true,
                'message' => 'Attachments reordered successfully',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to reorder attachments: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Copy attachments from one model to another.
     */
    public function copyAttachments(Model $sourceModel, Model $targetModel, ?string $category = null): array
    {
        $sourceAttachments = Attachment::where('attachable_id', $sourceModel->getKey())
            ->where('attachable_type', get_class($sourceModel))
            ->active()
            ->when($category, fn($q) => $q->byCategory($category))
            ->get();

        $copied = [];
        $failed = [];

        foreach ($sourceAttachments as $attachment) {
            try {
                // Copy file to new location
                $newFolder = $this->getStorageFolder($targetModel);
                $newPath = $newFolder . '/' . $attachment->stored_name;
                
                if (Storage::disk($attachment->disk)->exists($attachment->file_path)) {
                    Storage::disk($attachment->disk)->copy($attachment->file_path, $newPath);
                }

                // Create new attachment record
                $newAttachment = Attachment::create([
                    'company_id' => $attachment->company_id,
                    'attachable_id' => $targetModel->getKey(),
                    'attachable_type' => get_class($targetModel),
                    'original_name' => $attachment->original_name,
                    'stored_name' => $attachment->stored_name,
                    'file_path' => $newPath,
                    'disk' => $attachment->disk,
                    'mime_type' => $attachment->mime_type,
                    'file_size' => $attachment->file_size,
                    'file_extension' => $attachment->file_extension,
                    'file_hash' => $attachment->file_hash,
                    'category' => $attachment->category,
                    'description' => $attachment->description,
                    'sort_order' => $attachment->sort_order,
                    'uploaded_by' => Auth::id(),
                    'uploaded_at' => now(),
                    'status' => 1,
                ]);

                $copied[] = $newAttachment;

            } catch (\Exception $e) {
                $failed[] = [
                    'id' => $attachment->id,
                    'name' => $attachment->original_name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'success' => count($failed) === 0,
            'copied' => $copied,
            'failed' => $failed,
        ];
    }

    /**
     * Get storage statistics for a model.
     */
    public function getStorageStats(Model $model): array
    {
        $attachments = Attachment::where('attachable_id', $model->getKey())
            ->where('attachable_type', get_class($model))
            ->active()
            ->get();

        $totalSize = $attachments->sum('file_size');
        $countByCategory = $attachments->groupBy('category')->map->count();
        $countByExtension = $attachments->groupBy('file_extension')->map->count();

        return [
            'success' => true,
            'total_attachments' => $attachments->count(),
            'total_size_bytes' => $totalSize,
            'total_size_formatted' => $this->formatFileSize($totalSize),
            'by_category' => $countByCategory,
            'by_extension' => $countByExtension,
        ];
    }

    /**
     * Validate an uploaded file.
     */
    protected function validateFile(UploadedFile $file): array
    {
        // Check file size
        if ($file->getSize() > ($this->maxFileSize * 1024)) {
            return [
                'valid' => false,
                'error' => "File size exceeds maximum allowed ({$this->maxFileSize} KB)",
            ];
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            return [
                'valid' => false,
                'error' => 'File type not allowed: ' . $file->getMimeType(),
            ];
        }

        // Check extension
        if (!in_array(strtolower($file->getClientOriginalExtension()), $this->allowedExtensions)) {
            return [
                'valid' => false,
                'error' => 'File extension not allowed',
            ];
        }

        return ['valid' => true];
    }

    /**
     * Find an existing identical file.
     */
    protected function findExistingFile(string $fileHash, int $companyId): ?Attachment
    {
        return Attachment::where('file_hash', $fileHash)
            ->where('company_id', $companyId)
            ->where('status', 1)
            ->first();
    }

    /**
     * Get storage folder path for a model.
     */
    protected function getStorageFolder(Model $model): string
    {
        $modelName = Str::plural(Str::snake(class_basename($model)));
        $modelId = $model->getKey();
        $companyId = session('company_id');

        return "companies/{$companyId}/{$modelName}/{$modelId}";
    }

    /**
     * Generate a unique stored file name.
     */
    protected function generateStoredName(UploadedFile $file): string
    {
        $timestamp = now()->format('Ymd_His_u');
        $random = Str::random(8);
        $extension = strtolower($file->getClientOriginalExtension());

        return "{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Get the next sort order for a model.
     */
    protected function getNextSortOrder(Model $model): int
    {
        $maxOrder = Attachment::where('attachable_id', $model->getKey())
            ->where('attachable_type', get_class($model))
            ->max('sort_order');

        return ($maxOrder ?? -1) + 1;
    }

    /**
     * Format file size in human-readable format.
     */
    protected function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }

    /**
     * Set maximum file size.
     */
    public function setMaxFileSize(int $sizeInKb): self
    {
        $this->maxFileSize = $sizeInKb;
        return $this;
    }

    /**
     * Set allowed MIME types.
     */
    public function setAllowedMimeTypes(array $mimeTypes): self
    {
        $this->allowedMimeTypes = $mimeTypes;
        return $this;
    }

    /**
     * Add allowed MIME type.
     */
    public function addAllowedMimeType(string $mimeType): self
    {
        if (!in_array($mimeType, $this->allowedMimeTypes)) {
            $this->allowedMimeTypes[] = $mimeType;
        }
        return $this;
    }
}
