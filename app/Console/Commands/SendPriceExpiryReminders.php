<?php

namespace App\Console\Commands;

use App\Models\SupplierCatalog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendPriceExpiryReminders extends Command
{
    protected $signature = 'reminders:price-expiry {--days=7 : Notify when prices expire within this many days}';
    protected $description = 'Notify suppliers when catalog pricing is nearing its expiry date';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->addDays($days)->toDateString();
        $today = now()->toDateString();

        $expiring = SupplierCatalog::whereBetween('supcat_lastdate', [$today, $cutoff])->get();

        if ($expiring->isEmpty()) {
            $this->info('No expiring prices found.');
            return Command::SUCCESS;
        }

        foreach ($expiring as $entry) {
            // Placeholder hook: integrate mail/notification if SMTP templates exist.
            Log::info('Price expiry notice', [
                'supplier_id' => $entry->supcat_supplier,
                'item_code' => $entry->supcat_item_code,
                'expires' => $entry->supcat_lastdate,
            ]);
        }

        $this->info("Queued {$expiring->count()} price expiry reminders (see log).");
        return Command::SUCCESS;
    }
}
