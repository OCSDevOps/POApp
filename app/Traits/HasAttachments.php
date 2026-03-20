<?php

namespace App\Traits;

use App\Models\Attachment;
use App\Services\FileAttachmentService;

/**
 * Trait for models that support file attachments.
 */
trait HasAttachments
{
    /**
     * Boot the trait.
     */
    public static function bootHasAttachments(): void
    {
        // When a model is deleted, soft-delete all attachments
        static::deleting(function ($model) {
            if ($model->isForceDeleting()) {
                $model->attachments()->each(function ($attachment) {
                    $attachment->permanentlyDelete();
                });
            } else {
                $model->attachments()->each(function ($attachment) {
                    $attachment->softDelete();
                });
            }
        });
    }

    /**
     * Get all attachments for this model.
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable')
            ->ordered();
    }

    /**
     * Get active attachments only.
     */
    public function activeAttachments()
    {
        return $this->morphMany(Attachment::class, 'attachable')
            ->active()
            ->ordered();
    }

    /**
     * Get attachments by category.
     */
    public function attachmentsByCategory(string $category)
    {
        return $this->morphMany(Attachment::class, 'attachable')
            ->active()
            ->byCategory($category)
            ->ordered();
    }

    /**
     * Attach a file to this model.
     */
    public function attachFile($file, ?string $category = null, ?string $description = null): array
    {
        $service = app(FileAttachmentService::class);
        return $service->attachFile($this, $file, $category, $description);
    }

    /**
     * Attach multiple files to this model.
     */
    public function attachFiles(array $files, ?string $category = null): array
    {
        $service = app(FileAttachmentService::class);
        return $service->attachMultiple($this, $files, $category);
    }

    /**
     * Get the number of attachments.
     */
    public function attachmentCount(): int
    {
        return $this->morphMany(Attachment::class, 'attachable')
            ->active()
            ->count();
    }

    /**
     * Check if model has any attachments.
     */
    public function hasAttachments(): bool
    {
        return $this->attachmentCount() > 0;
    }
}
