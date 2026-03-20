<?php

namespace App\Jobs;

use App\Models\ApprovalRequest;
use App\Models\User;
use App\Notifications\ApprovalPendingNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendApprovalPendingNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public array $recipientUserIds,
        public int $approvalRequestId,
        public string $entityType,
        public string $entityNumber,
        public float $amount,
        public int $level
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $request = ApprovalRequest::find($this->approvalRequestId);
        if (!$request) {
            return;
        }

        $users = User::whereIn('id', $this->recipientUserIds)->get();
        if ($users->isEmpty()) {
            return;
        }

        Notification::sendNow($users, new ApprovalPendingNotification(
            $request,
            $this->entityType,
            $this->entityNumber,
            $this->amount,
            $this->level
        ));

        Log::info('Queued approval pending notifications delivered', [
            'approval_request_id' => $this->approvalRequestId,
            'level' => $this->level,
            'recipients' => $users->count(),
        ]);
    }
}
