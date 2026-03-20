<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Services\FileAttachmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttachmentController extends Controller
{
    protected FileAttachmentService $attachmentService;

    public function __construct(FileAttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    /**
     * Upload attachment via AJAX.
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,gif,doc,docx,xls,xlsx,txt,csv,zip|max:10240',
            'attachable_type' => 'required|string',
            'attachable_id' => 'required|integer',
            'category' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Resolve the model
        $modelClass = $this->resolveModelClass($request->attachable_type);
        if (!$modelClass) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid attachable type',
            ], 400);
        }

        $model = $modelClass::find($request->attachable_id);
        if (!$model) {
            return response()->json([
                'success' => false,
                'error' => 'Model not found',
            ], 404);
        }

        // Check company ownership
        if (method_exists($model, 'company_id') && $model->company_id != session('company_id')) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 403);
        }

        // Upload file
        $result = $this->attachmentService->attachFile(
            $model,
            $request->file('file'),
            $request->category,
            $request->description
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 400);
        }

        $attachment = $result['attachment'];

        return response()->json([
            'success' => true,
            'attachment' => [
                'id' => $attachment->id,
                'original_name' => $attachment->original_name,
                'file_size_formatted' => $attachment->getFileSizeFormatted(),
                'mime_type' => $attachment->mime_type,
                'icon_class' => $attachment->getIconClass(),
                'url' => $attachment->getUrl(),
                'category' => $attachment->category,
                'description' => $attachment->description,
                'uploaded_at' => $attachment->uploaded_at->format('Y-m-d H:i'),
            ],
            'message' => 'File uploaded successfully',
        ]);
    }

    /**
     * Download an attachment.
     */
    public function download($id)
    {
        try {
            return $this->attachmentService->downloadAttachment($id, false);
        } catch (\Exception $e) {
            return back()->with('error', 'File not found or access denied.');
        }
    }

    /**
     * View attachment inline.
     */
    public function view($id)
    {
        try {
            return $this->attachmentService->downloadAttachment($id, true);
        } catch (\Exception $e) {
            abort(404, 'File not found');
        }
    }

    /**
     * Delete an attachment.
     */
    public function destroy($id)
    {
        $result = $this->attachmentService->deleteAttachment($id, false);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return back()->with('success', 'Attachment deleted successfully.');
        }

        return back()->with('error', $result['error']);
    }

    /**
     * Delete multiple attachments.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        $result = $this->attachmentService->deleteAttachments($request->ids, false);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return back()->with('success', $result['deleted'] . ' attachment(s) deleted.');
        }

        return back()->with('error', 'Some attachments could not be deleted.');
    }

    /**
     * Reorder attachments.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'attachment_ids' => 'required|array',
            'attachment_ids.*' => 'integer',
        ]);

        // Get the first attachment to find the parent model
        $firstAttachment = Attachment::find($request->attachment_ids[0] ?? 0);
        if (!$firstAttachment) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid attachment IDs',
            ]);
        }

        $result = $this->attachmentService->reorderAttachments(
            $firstAttachment->attachable,
            $request->attachment_ids
        );

        return response()->json($result);
    }

    /**
     * Get attachments for a model.
     */
    public function list(Request $request)
    {
        $request->validate([
            'attachable_type' => 'required|string',
            'attachable_id' => 'required|integer',
        ]);

        $modelClass = $this->resolveModelClass($request->attachable_type);
        if (!$modelClass) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid attachable type',
            ]);
        }

        $model = $modelClass::find($request->attachable_id);
        if (!$model) {
            return response()->json([
                'success' => false,
                'error' => 'Model not found',
            ]);
        }

        $category = $request->category;
        $result = $this->attachmentService->getAttachments($model, $category);

        if (!$result['success']) {
            return response()->json($result);
        }

        $attachments = $result['attachments']->map(function ($attachment) {
            return [
                'id' => $attachment->id,
                'original_name' => $attachment->original_name,
                'file_size_formatted' => $attachment->getFileSizeFormatted(),
                'mime_type' => $attachment->mime_type,
                'icon_class' => $attachment->getIconClass(),
                'url' => $attachment->getUrl(),
                'category' => $attachment->category,
                'description' => $attachment->description,
                'is_image' => $attachment->isImage(),
                'uploaded_at' => $attachment->uploaded_at?->format('Y-m-d H:i'),
                'uploaded_by' => $attachment->uploader?->name,
                'sort_order' => $attachment->sort_order,
            ];
        });

        return response()->json([
            'success' => true,
            'attachments' => $attachments,
            'count' => $result['count'],
        ]);
    }

    /**
     * Resolve model class from type string.
     */
    protected function resolveModelClass(string $type): ?string
    {
        $mapping = [
            'purchase_order' => 'App\\Models\\PurchaseOrder',
            'po' => 'App\\Models\\PurchaseOrder',
            'rfq' => 'App\\Models\\Rfq',
            'budget_change_order' => 'App\\Models\\BudgetChangeOrder',
            'bco' => 'App\\Models\\BudgetChangeOrder',
            'po_change_order' => 'App\\Models\\PoChangeOrder',
            'poco' => 'App\\Models\\PoChangeOrder',
        ];

        $class = $mapping[strtolower($type)] ?? null;
        
        if (!$class && class_exists('App\\Models\\' . $type)) {
            $class = 'App\\Models\\' . $type;
        }

        return $class;
    }
}
