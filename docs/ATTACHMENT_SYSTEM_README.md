# File Attachment System Documentation

## Overview

The POApp File Attachment System provides a **polymorphic, multi-tenant file upload and management solution** that supports attaching files to any entity in the system (Purchase Orders, RFQs, Change Orders, etc.).

## Features

- ✅ **Polymorphic Attachments**: One table supports all entity types
- ✅ **Multi-Tenant**: Files are isolated by company
- ✅ **File Deduplication**: SHA-256 hash prevents storing duplicate files
- ✅ **Category Support**: Organize attachments by type (invoice, contract, photo, etc.)
- ✅ **Drag & Drop Upload**: AJAX-based file upload with progress bar
- ✅ **Image Preview**: Built-in modal for viewing images
- ✅ **Secure Downloads**: Company-scoped access control
- ✅ **Soft Delete**: Mark as deleted without removing files immediately
- ✅ **Storage Statistics**: Track usage per entity

## Architecture

### Database Schema

```sql
table: attachments
- id (PK)
- company_id (indexed)
- attachable_id + attachable_type (polymorphic, indexed)
- original_name (user-friendly name)
- stored_name (unique generated name)
- file_path (storage path)
- disk (storage disk name)
- mime_type
- file_size
- file_extension
- file_hash (SHA-256 for deduplication)
- category (optional: invoice, contract, photo, etc.)
- description (optional)
- sort_order (for manual ordering)
- uploaded_by (user ID)
- uploaded_at
- status (1=active, 0=deleted)
- timestamps
```

### File Structure

```
storage/app/public/
└── companies/
    └── {company_id}/
        ├── purchase_orders/
        │   └── {po_id}/
        │       └── {timestamp}_{random}.pdf
        ├── rfqs/
        │   └── {rfq_id}/
        └── budget_change_orders/
            └── {bco_id}/
```

## Components

### 1. Attachment Model (`app/Models/Attachment.php`)

```php
$attachment = Attachment::find(1);

// File operations
$attachment->fileExists();           // Check if file exists on disk
$attachment->getUrl();               // Get public URL
$attachment->getFullPath();          // Get absolute path
$attachment->downloadResponse();     // Download response
$attachment->inlineResponse();       // Inline display response

// Type checking
$attachment->isImage();              // Is image file?
$attachment->isPdf();                // Is PDF?
$attachment->getIconClass();         // Font Awesome icon class

// Formatting
$attachment->getFileSizeFormatted(); // "1.5 MB"

// Deletion
$attachment->softDelete();           // Mark as deleted
$attachment->permanentlyDelete();    // Delete file and record
```

### 2. HasAttachments Trait (`app/Traits/HasAttachments.php`)

Add to any model to enable attachments:

```php
class PurchaseOrder extends Model
{
    use HasAttachments;
}

// Usage:
$po = PurchaseOrder::find(1);

// Get attachments
$po->attachments;                    // All attachments
$po->activeAttachments;              // Active only
$po->attachmentsByCategory('invoice'); // Filter by category
$po->attachmentCount();              // Count
$po->hasAttachments();               // Boolean check

// Attach files
$po->attachFile($uploadedFile, 'invoice', 'Description');
$po->attachFiles([$file1, $file2], 'contract');
```

### 3. FileAttachmentService (`app/Services/FileAttachmentService.php`)

```php
$service = app(FileAttachmentService::class);

// Single file upload
$result = $service->attachFile($model, $file, $category, $description);

// Multiple files
$result = $service->attachMultiple($model, [$file1, $file2], $category);

// Get attachments
$result = $service->getAttachments($model, $category);

// Delete
$service->deleteAttachment($attachmentId, $permanent = false);
$service->deleteAttachments([$id1, $id2], $permanent = true);

// Reorder
$service->reorderAttachments($model, [$id1, $id2, $id3]);

// Copy attachments between models
$service->copyAttachments($sourceModel, $targetModel, $category);

// Statistics
$stats = $service->getStorageStats($model);
```

### 4. AttachmentController (`app/Http/Controllers/Admin/AttachmentController.php`)

Routes:

| Method | Route | Description |
|--------|-------|-------------|
| POST | `/admincontrol/attachments/upload` | AJAX upload |
| POST | `/admincontrol/attachments/list` | List attachments |
| POST | `/admincontrol/attachments/reorder` | Reorder attachments |
| GET | `/admincontrol/attachments/{id}/download` | Download file |
| GET | `/admincontrol/attachments/{id}/view` | View inline |
| DELETE | `/admincontrol/attachments/{id}` | Delete attachment |
| POST | `/admincontrol/attachments/delete-multiple` | Bulk delete |

### 5. Blade Component (`resources/views/components/attachments/manager.blade.php`)

Include in any form:

```blade
@include('components.attachments.manager', [
    'attachableType' => 'purchase_order',
    'attachableId' => $purchaseOrder->porder_id,
    'attachments' => $purchaseOrder->activeAttachments,
    'categories' => ['quote', 'contract', 'invoice', 'photo'],
    'maxFiles' => 10,
    'maxFileSize' => 10240, // KB
])
```

## Supported Models

The following models now support attachments via the `HasAttachments` trait:

- ✅ `PurchaseOrder`
- ✅ `Rfq`
- ✅ `BudgetChangeOrder`
- ✅ `PoChangeOrder`

To add to a new model:

```php
use App\Traits\HasAttachments;

class YourModel extends Model
{
    use HasAttachments;
}
```

## Migration from Legacy System

The system includes a migration command for existing `PurchaseOrderAttachment` records:

```bash
# Dry run (preview changes)
php artisan attachments:migrate-to-polymorphic --dry-run

# Execute migration
php artisan attachments:migrate-to-polymorphic

# Custom batch size
php artisan attachments:migrate-to-polymorphic --batch=500
```

## Configuration

### File Size Limits

Default: 10 MB per file

```php
// In controller or service
$service->setMaxFileSize(20480); // 20 MB
```

### Allowed File Types

Default:
- Documents: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV
- Images: JPG, JPEG, PNG, GIF, BMP, WEBP, SVG
- Archives: ZIP, RAR, 7Z

```php
// Add custom MIME type
$service->addAllowedMimeType('application/json');

// Or set complete list
$service->setAllowedMimeTypes(['application/pdf', 'image/jpeg']);
```

## Usage Examples

### In Controller: Store with Attachments

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'project_id' => 'required',
        'attachments' => 'nullable|array|max:10',
        'attachments.*' => 'file|mimes:pdf,jpg,png|max:10240',
    ]);

    DB::transaction(function () use ($request, $validated) {
        // Create entity
        $po = PurchaseOrder::create([...]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            $service = app(FileAttachmentService::class);
            $service->attachMultiple($po, $request->file('attachments'));
        }
    });
}
```

### In Controller: Show with Attachments

```php
public function show($id)
{
    $po = PurchaseOrder::with(['attachments'])->findOrFail($id);
    
    return view('admin.porder.show', compact('po'));
}
```

### In View: Display Attachments

```blade
@if($purchaseOrder->hasAttachments())
    <h6>Attachments ({{ $purchaseOrder->attachmentCount() }})</h6>
    
    @foreach($purchaseOrder->attachments as $attachment)
        <div class="attachment">
            <i class="fas {{ $attachment->getIconClass() }}"></i>
            <a href="{{ route('admin.attachments.download', $attachment->id) }}">
                {{ $attachment->original_name }}
            </a>
            <span>{{ $attachment->getFileSizeFormatted() }}</span>
        </div>
    @endforeach
@endif
```

### AJAX Upload

```javascript
const formData = new FormData();
formData.append('file', fileInput.files[0]);
formData.append('attachable_type', 'purchase_order');
formData.append('attachable_id', 123);
formData.append('category', 'invoice');

fetch('/admincontrol/attachments/upload', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('File uploaded:', data.attachment);
    }
});
```

## Security Features

1. **Company Isolation**: Users can only access attachments from their own company
2. **CSRF Protection**: All upload routes require CSRF token
3. **File Type Validation**: MIME type and extension checking
4. **Size Limits**: Configurable per-file size limits
5. **Storage Path Isolation**: Files stored in company-scoped directories
6. **Soft Delete**: Files can be recovered if deleted accidentally

## Testing

Run attachment system tests:

```bash
# All attachment tests
./vendor/bin/phpunit tests/Unit/Services/FileAttachmentServiceTest.php

# All tests
./vendor/bin/phpunit
```

## Future Enhancements

- [ ] Virus scanning integration
- [ ] Automatic image resizing/thumbnails
- [ ] OCR for document searchability
- [ ] Version control for attachments
- [ ] Bulk download as ZIP
- [ ] S3 storage support
- [ ] CDN integration for faster downloads
