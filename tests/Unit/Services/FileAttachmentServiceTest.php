<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\FileAttachmentService;
use App\Models\Attachment;
use App\Models\PurchaseOrder;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileAttachmentServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected FileAttachmentService $service;
    protected Company $company;
    protected User $user;
    protected Project $project;
    protected Supplier $supplier;
    protected PurchaseOrder $purchaseOrder;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new FileAttachmentService();
        Storage::fake('public');
        
        // Create test data
        $this->company = Company::create([
            'name' => 'Test Company',
            'subdomain' => 'test',
            'status' => 1,
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
            'company_id' => $this->company->id,
        ]);

        $this->project = Project::create([
            'proj_name' => 'Test Project',
            'proj_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->supplier = Supplier::create([
            'sup_name' => 'Test Supplier',
            'sup_email' => 'supplier@test.com',
            'sup_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->purchaseOrder = PurchaseOrder::create([
            'porder_no' => 'PO000001',
            'porder_project_ms' => $this->project->proj_id,
            'porder_supplier_ms' => $this->supplier->sup_id,
            'porder_total_amount' => 10000.00,
            'porder_status' => 1,
            'porder_createdate' => now(),
            'company_id' => $this->company->id,
        ]);

        // Set company context
        session(['company_id' => $this->company->id]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_attaches_single_file_to_model()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $result = $this->service->attachFile($this->purchaseOrder, $file);

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Attachment::class, $result['attachment']);
        $this->assertEquals('document.pdf', $result['attachment']->original_name);
        $this->assertEquals('application/pdf', $result['attachment']->mime_type);
        
        Storage::disk('public')->assertExists($result['attachment']->file_path);
    }

    /** @test */
    public function it_attaches_file_with_category_and_description()
    {
        $file = UploadedFile::fake()->image('photo.jpg');

        $result = $this->service->attachFile(
            $this->purchaseOrder, 
            $file, 
            'invoice',
            'Invoice for materials'
        );

        $this->assertTrue($result['success']);
        $this->assertEquals('invoice', $result['attachment']->category);
        $this->assertEquals('Invoice for materials', $result['attachment']->description);
    }

    /** @test */
    public function it_attaches_multiple_files()
    {
        $files = [
            UploadedFile::fake()->create('doc1.pdf', 50),
            UploadedFile::fake()->create('doc2.pdf', 75),
            UploadedFile::fake()->image('image.jpg'),
        ];

        $result = $this->service->attachMultiple($this->purchaseOrder, $files);

        $this->assertTrue($result['success']);
        $this->assertCount(3, $result['attached']);
        $this->assertCount(0, $result['failed']);
    }

    /** @test */
    public function it_rejects_file_exceeding_max_size()
    {
        $this->service->setMaxFileSize(1); // 1 KB
        $file = UploadedFile::fake()->create('large.pdf', 2000); // 2 MB

        $result = $this->service->attachFile($this->purchaseOrder, $file);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('exceeds maximum', $result['error']);
    }

    /** @test */
    public function it_rejects_invalid_file_type()
    {
        $file = UploadedFile::fake()->create('malicious.exe', 100);

        $result = $this->service->attachFile($this->purchaseOrder, $file);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('File type not allowed', $result['error']);
    }

    /** @test */
    public function it_gets_attachments_for_model()
    {
        // Create attachments
        Attachment::create([
            'company_id' => $this->company->id,
            'attachable_id' => $this->purchaseOrder->porder_id,
            'attachable_type' => PurchaseOrder::class,
            'original_name' => 'doc1.pdf',
            'stored_name' => 'doc1.pdf',
            'file_path' => 'test/doc1.pdf',
            'disk' => 'public',
            'file_size' => 1000,
            'uploaded_by' => $this->user->id,
            'uploaded_at' => now(),
            'status' => 1,
        ]);

        Attachment::create([
            'company_id' => $this->company->id,
            'attachable_id' => $this->purchaseOrder->porder_id,
            'attachable_type' => PurchaseOrder::class,
            'original_name' => 'doc2.pdf',
            'stored_name' => 'doc2.pdf',
            'file_path' => 'test/doc2.pdf',
            'disk' => 'public',
            'file_size' => 2000,
            'uploaded_by' => $this->user->id,
            'uploaded_at' => now(),
            'status' => 1,
        ]);

        $result = $this->service->getAttachments($this->purchaseOrder);

        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['attachments']);
        $this->assertEquals(2, $result['count']);
    }

    /** @test */
    public function it_gets_attachments_by_category()
    {
        Attachment::create([
            'company_id' => $this->company->id,
            'attachable_id' => $this->purchaseOrder->porder_id,
            'attachable_type' => PurchaseOrder::class,
            'original_name' => 'invoice1.pdf',
            'stored_name' => 'invoice1.pdf',
            'file_path' => 'test/invoice1.pdf',
            'disk' => 'public',
            'category' => 'invoice',
            'file_size' => 1000,
            'uploaded_by' => $this->user->id,
            'uploaded_at' => now(),
            'status' => 1,
        ]);

        Attachment::create([
            'company_id' => $this->company->id,
            'attachable_id' => $this->purchaseOrder->porder_id,
            'attachable_type' => PurchaseOrder::class,
            'original_name' => 'contract.pdf',
            'stored_name' => 'contract.pdf',
            'file_path' => 'test/contract.pdf',
            'disk' => 'public',
            'category' => 'contract',
            'file_size' => 2000,
            'uploaded_by' => $this->user->id,
            'uploaded_at' => now(),
            'status' => 1,
        ]);

        $result = $this->service->getAttachments($this->purchaseOrder, 'invoice');

        $this->assertCount(1, $result['attachments']);
        $this->assertEquals('invoice', $result['attachments']->first()->category);
    }

    /** @test */
    public function it_soft_deletes_attachment()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);
        $attachResult = $this->service->attachFile($this->purchaseOrder, $file);
        $attachmentId = $attachResult['attachment']->id;

        $result = $this->service->deleteAttachment($attachmentId, false);

        $this->assertTrue($result['success']);
        $this->assertEquals(0, Attachment::find($attachmentId)->status);
        
        // File should still exist
        Storage::disk('public')->assertExists($attachResult['attachment']->file_path);
    }

    /** @test */
    public function it_permanently_deletes_attachment()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);
        $attachResult = $this->service->attachFile($this->purchaseOrder, $file);
        $attachmentId = $attachResult['attachment']->id;

        $result = $this->service->deleteAttachment($attachmentId, true);

        $this->assertTrue($result['success']);
        $this->assertNull(Attachment::find($attachmentId));
        
        // File should be deleted
        Storage::disk('public')->assertMissing($attachResult['attachment']->file_path);
    }

    /** @test */
    public function it_prevents_deleting_attachment_from_other_company()
    {
        $otherCompany = Company::create([
            'name' => 'Other Company',
            'subdomain' => 'other',
            'status' => 1,
        ]);

        $attachment = Attachment::create([
            'company_id' => $otherCompany->id,
            'attachable_id' => $this->purchaseOrder->porder_id,
            'attachable_type' => PurchaseOrder::class,
            'original_name' => 'doc.pdf',
            'stored_name' => 'doc.pdf',
            'file_path' => 'test/doc.pdf',
            'disk' => 'public',
            'file_size' => 1000,
            'status' => 1,
        ]);

        $result = $this->service->deleteAttachment($attachment->id, true);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Unauthorized', $result['error']);
    }

    /** @test */
    public function it_reorders_attachments()
    {
        $attachments = [];
        for ($i = 1; $i <= 3; $i++) {
            $attachments[] = Attachment::create([
                'company_id' => $this->company->id,
                'attachable_id' => $this->purchaseOrder->porder_id,
                'attachable_type' => PurchaseOrder::class,
                'original_name' => "doc{$i}.pdf",
                'stored_name' => "doc{$i}.pdf",
                'file_path' => "test/doc{$i}.pdf",
                'disk' => 'public',
                'sort_order' => $i,
                'file_size' => 1000,
                'status' => 1,
            ]);
        }

        // Reorder: 3, 1, 2
        $newOrder = [$attachments[2]->id, $attachments[0]->id, $attachments[1]->id];
        $result = $this->service->reorderAttachments($this->purchaseOrder, $newOrder);

        $this->assertTrue($result['success']);
        
        $this->assertEquals(0, Attachment::find($attachments[2]->id)->sort_order);
        $this->assertEquals(1, Attachment::find($attachments[0]->id)->sort_order);
        $this->assertEquals(2, Attachment::find($attachments[1]->id)->sort_order);
    }

    /** @test */
    public function it_gets_storage_statistics()
    {
        Attachment::create([
            'company_id' => $this->company->id,
            'attachable_id' => $this->purchaseOrder->porder_id,
            'attachable_type' => PurchaseOrder::class,
            'original_name' => 'invoice1.pdf',
            'stored_name' => 'invoice1.pdf',
            'file_path' => 'test/invoice1.pdf',
            'disk' => 'public',
            'category' => 'invoice',
            'file_extension' => 'pdf',
            'file_size' => 1024000, // 1 MB
            'status' => 1,
        ]);

        Attachment::create([
            'company_id' => $this->company->id,
            'attachable_id' => $this->purchaseOrder->porder_id,
            'attachable_type' => PurchaseOrder::class,
            'original_name' => 'doc.xlsx',
            'stored_name' => 'doc.xlsx',
            'file_path' => 'test/doc.xlsx',
            'disk' => 'public',
            'category' => 'contract',
            'file_extension' => 'xlsx',
            'file_size' => 512000, // 500 KB
            'status' => 1,
        ]);

        $result = $this->service->getStorageStats($this->purchaseOrder);

        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['total_attachments']);
        $this->assertEquals(1536000, $result['total_size_bytes']);
        $this->assertEquals('1.46 MB', $result['total_size_formatted']);
    }

    /** @test */
    public function it_copies_attachments_to_another_model()
    {
        // Create source attachments
        Storage::disk('public')->put('source/doc.pdf', 'test content');
        
        $sourceAttachment = Attachment::create([
            'company_id' => $this->company->id,
            'attachable_id' => $this->purchaseOrder->porder_id,
            'attachable_type' => PurchaseOrder::class,
            'original_name' => 'doc.pdf',
            'stored_name' => 'doc.pdf',
            'file_path' => 'source/doc.pdf',
            'disk' => 'public',
            'file_size' => 1000,
            'status' => 1,
        ]);

        $targetPo = PurchaseOrder::create([
            'porder_no' => 'PO000002',
            'porder_project_ms' => $this->project->proj_id,
            'porder_supplier_ms' => $this->supplier->sup_id,
            'porder_total_amount' => 5000.00,
            'porder_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $result = $this->service->copyAttachments($this->purchaseOrder, $targetPo);

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['copied']);
        $this->assertCount(0, $result['failed']);
        
        // Target should have the attachment
        $targetAttachments = Attachment::where('attachable_id', $targetPo->porder_id)
            ->where('attachable_type', PurchaseOrder::class)
            ->get();
        $this->assertCount(1, $targetAttachments);
    }

    /** @test */
    public function it_generates_correct_file_size_format()
    {
        $file = UploadedFile::fake()->create('document.pdf', 1024); // 1 MB
        $result = $this->service->attachFile($this->purchaseOrder, $file);

        $this->assertEquals('1.00 MB', $result['attachment']->getFileSizeFormatted());
    }

    /** @test */
    public function it_identifies_file_types_correctly()
    {
        $pdfFile = UploadedFile::fake()->create('document.pdf', 100);
        $pdfResult = $this->service->attachFile($this->purchaseOrder, $pdfFile);

        $this->assertTrue($pdfResult['attachment']->isPdf());
        $this->assertFalse($pdfResult['attachment']->isImage());
        $this->assertEquals('fa-file-pdf', $pdfResult['attachment']->getIconClass());

        $imageFile = UploadedFile::fake()->image('photo.jpg');
        $imageResult = $this->service->attachFile($this->purchaseOrder, $imageFile);

        $this->assertTrue($imageResult['attachment']->isImage());
        $this->assertFalse($imageResult['attachment']->isPdf());
        $this->assertEquals('fa-file-image', $imageResult['attachment']->getIconClass());
    }

    /** @test */
    public function attachment_has_hasattachments_trait()
    {
        $this->assertTrue(method_exists($this->purchaseOrder, 'attachments'));
        $this->assertTrue(method_exists($this->purchaseOrder, 'attachFile'));
        $this->assertTrue(method_exists($this->purchaseOrder, 'attachmentCount'));
    }
}
