<?php

namespace App\Console\Commands;

use App\Models\Attachment;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderAttachment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateAttachmentsToPolymorphic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attachments:migrate-to-polymorphic 
                            {--dry-run : Preview changes without executing}
                            {--batch=100 : Number of records to process per batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing PurchaseOrderAttachment records to new polymorphic attachments table';

    /**
     * Statistics counter.
     */
    protected array $stats = [
        'processed' => 0,
        'migrated' => 0,
        'skipped' => 0,
        'errors' => 0,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $batchSize = (int) $this->option('batch');

        $this->info('========================================');
        $this->info('Attachment Migration Tool');
        $this->info('========================================');
        
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        // Count total records
        $totalCount = PurchaseOrderAttachment::count();
        
        if ($totalCount === 0) {
            $this->info('No legacy attachments found. Nothing to migrate.');
            return 0;
        }

        $this->info("Found {$totalCount} legacy attachments to process.");
        
        if (!$dryRun && !$this->confirm('Do you want to proceed with the migration?')) {
            return 0;
        }

        // Process in batches
        $bar = $this->output->createProgressBar($totalCount);
        $bar->start();

        PurchaseOrderAttachment::chunk($batchSize, function ($attachments) use ($dryRun, $bar) {
            foreach ($attachments as $legacyAttachment) {
                $this->processAttachment($legacyAttachment, $dryRun);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        // Display statistics
        $this->info('========================================');
        $this->info('Migration Statistics');
        $this->info('========================================');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Processed', $this->stats['processed']],
                ['Successfully Migrated', $this->stats['migrated']],
                ['Skipped (Already Exists)', $this->stats['skipped']],
                ['Errors', $this->stats['errors']],
            ]
        );

        return 0;
    }

    /**
     * Process a single legacy attachment.
     */
    protected function processAttachment(PurchaseOrderAttachment $legacy, bool $dryRun): void
    {
        $this->stats['processed']++;

        try {
            // Check if already migrated (by path)
            $existing = Attachment::where('file_path', $legacy->po_attachment_path)
                ->where('company_id', $legacy->company_id)
                ->first();

            if ($existing) {
                $this->stats['skipped']++;
                return;
            }

            // Get file extension from original name
            $extension = pathinfo($legacy->po_attachment_original_name, PATHINFO_EXTENSION);

            // Generate file hash if file exists
            $fileHash = null;
            $fullPath = Storage::disk('public')->path($legacy->po_attachment_path);
            if (file_exists($fullPath)) {
                $fileHash = hash_file('sha256', $fullPath);
            }

            if (!$dryRun) {
                Attachment::create([
                    'company_id' => $legacy->company_id,
                    'attachable_id' => $legacy->po_attachment_porder_ms,
                    'attachable_type' => PurchaseOrder::class,
                    'original_name' => $legacy->po_attachment_original_name,
                    'stored_name' => basename($legacy->po_attachment_path),
                    'file_path' => $legacy->po_attachment_path,
                    'disk' => 'public',
                    'mime_type' => $legacy->po_attachment_mime,
                    'file_size' => $legacy->po_attachment_size,
                    'file_extension' => strtolower($extension),
                    'file_hash' => $fileHash,
                    'category' => null,
                    'description' => null,
                    'sort_order' => 0,
                    'uploaded_by' => $legacy->po_attachment_createby,
                    'uploaded_at' => $legacy->po_attachment_createdate,
                    'status' => $legacy->po_attachment_status,
                ]);
            }

            $this->stats['migrated']++;

        } catch (\Exception $e) {
            $this->stats['errors']++;
            $this->error("Error processing attachment {$legacy->po_attachment_id}: {$e->getMessage()}");
        }
    }
}
