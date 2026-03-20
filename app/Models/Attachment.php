<?php

namespace App\Models;

use App\Traits\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory, CompanyScope;

    protected $table = 'attachments';

    protected $fillable = [
        'company_id',
        'attachable_id',
        'attachable_type',
        'original_name',
        'stored_name',
        'file_path',
        'disk',
        'mime_type',
        'file_size',
        'file_extension',
        'file_hash',
        'category',
        'description',
        'sort_order',
        'uploaded_by',
        'uploaded_at',
        'status',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'sort_order' => 'integer',
        'status' => 'integer',
        'uploaded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent attachable model.
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded this attachment.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }

    /**
     * Scope: Active attachments only.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope: Filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Ordered by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('uploaded_at', 'desc');
    }

    /**
     * Get the full storage path.
     */
    public function getFullPath(): string
    {
        return Storage::disk($this->disk)->path($this->file_path);
    }

    /**
     * Check if file exists on disk.
     */
    public function fileExists(): bool
    {
        return Storage::disk($this->disk)->exists($this->file_path);
    }

    /**
     * Get the file URL.
     */
    public function getUrl(): string
    {
        return Storage::disk($this->disk)->url($this->file_path);
    }

    /**
     * Get file size in human-readable format.
     */
    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Check if file is an image.
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if file is a PDF.
     */
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    /**
     * Get icon class based on file type.
     */
    public function getIconClass(): string
    {
        return match (true) {
            $this->isPdf() => 'fa-file-pdf',
            $this->isImage() => 'fa-file-image',
            str_contains($this->mime_type, 'word') || $this->file_extension === 'doc' || $this->file_extension === 'docx' => 'fa-file-word',
            str_contains($this->mime_type, 'excel') || str_contains($this->mime_type, 'sheet') || $this->file_extension === 'xls' || $this->file_extension === 'xlsx' => 'fa-file-excel',
            str_contains($this->mime_type, 'powerpoint') || str_contains($this->mime_type, 'presentation') => 'fa-file-powerpoint',
            str_contains($this->mime_type, 'zip') || str_contains($this->mime_type, 'compressed') => 'fa-file-archive',
            str_contains($this->mime_type, 'text') || $this->file_extension === 'txt' => 'fa-file-alt',
            default => 'fa-file',
        };
    }

    /**
     * Soft delete the attachment (mark as deleted without removing file).
     */
    public function softDelete(): void
    {
        $this->update(['status' => 0]);
    }

    /**
     * Permanently delete the attachment and file.
     */
    public function permanentlyDelete(): bool
    {
        try {
            if ($this->fileExists()) {
                Storage::disk($this->disk)->delete($this->file_path);
            }
            return $this->delete();
        } catch (\Exception $e) {
            \Log::error('Failed to delete attachment', [
                'attachment_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get download response for this attachment.
     */
    public function downloadResponse()
    {
        if (!$this->fileExists()) {
            abort(404, 'File not found');
        }

        return Storage::disk($this->disk)->download(
            $this->file_path,
            $this->original_name
        );
    }

    /**
     * Get inline display response for this attachment.
     */
    public function inlineResponse()
    {
        if (!$this->fileExists()) {
            abort(404, 'File not found');
        }

        return Storage::disk($this->disk)->response(
            $this->file_path,
            $this->original_name,
            ['Content-Disposition' => 'inline']
        );
    }
}
