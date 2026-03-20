<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalPendingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $approvalRequest;
    protected $entityType;
    protected $entityNumber;
    protected $amount;
    protected $level;

    public function __construct($approvalRequest, $entityType, $entityNumber, $amount, $level = 1)
    {
        $this->approvalRequest = $approvalRequest;
        $this->entityType = $entityType;
        $this->entityNumber = $entityNumber;
        $this->amount = $amount;
        $this->level = $level;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $typeLabels = [
            'BudgetChangeOrder' => 'Budget Change Order',
            'PoChangeOrder' => 'PO Change Order',
            'PurchaseOrder' => 'Purchase Order',
            'budget_co' => 'Budget Change Order',
            'po_co' => 'PO Change Order',
            'po' => 'Purchase Order',
        ];

        $label = $typeLabels[$this->entityType] ?? $this->entityType;
        $requestId = $this->approvalRequest->request_id ?? $this->approvalRequest->ar_id;

        return (new MailMessage)
            ->subject("Approval Required: {$label} {$this->entityNumber}")
            ->greeting('Approval Required')
            ->line("A {$label} requires your approval.")
            ->line("**Number:** {$this->entityNumber}")
            ->line("**Amount:** $" . number_format($this->amount, 2))
            ->line("**Approval Level:** Level {$this->level}")
            ->action('Review & Approve', route('admin.approvals.show', $requestId))
            ->line('Please review and take action as soon as possible.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'approval_pending',
            'approval_request_id' => $this->approvalRequest->request_id ?? $this->approvalRequest->ar_id,
            'entity_type' => $this->entityType,
            'entity_number' => $this->entityNumber,
            'amount' => $this->amount,
            'level' => $this->level,
            'submitted_at' => $this->approvalRequest->submitted_at ?? $this->approvalRequest->ar_submitted_at,
        ];
    }
}
