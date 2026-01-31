<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ChangeOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $changeOrder;
    protected $type; // 'bco' or 'pco'
    protected $action; // 'created', 'approved', 'rejected', 'cancelled'
    protected $actorName;

    public function __construct($changeOrder, $type, $action, $actorName)
    {
        $this->changeOrder = $changeOrder;
        $this->type = $type;
        $this->action = $action;
        $this->actorName = $actorName;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $isBCO = $this->type === 'bco';
        $label = $isBCO ? 'Budget Change Order' : 'PO Change Order';
        $number = $isBCO ? $this->changeOrder->bco_number : $this->changeOrder->poco_number;
        
        $statusLabels = [
            'created' => 'Created',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
        ];

        $status = $statusLabels[$this->action] ?? ucfirst($this->action);
        $color = $this->action === 'approved' ? '#28a745' : ($this->action === 'rejected' ? '#dc3545' : '#17a2b8');

        $message = (new MailMessage)
            ->subject("{$label} {$status}: {$number}")
            ->greeting("{$label} {$status}")
            ->line("{$label} **{$number}** has been {$this->action} by {$this->actorName}.");

        if ($isBCO) {
            $message->line("**Cost Code:** {$this->changeOrder->costCode->cc_no} - {$this->changeOrder->costCode->cc_description}");
            $message->line("**Previous Amount:** $" . number_format($this->changeOrder->bco_previous_amount, 2));
            $message->line("**New Amount:** $" . number_format($this->changeOrder->bco_new_amount, 2));
            $message->line("**Change:** $" . number_format($this->changeOrder->bco_new_amount - $this->changeOrder->bco_previous_amount, 2));
            $message->action('View BCO', route('admin.budget-change-orders.show', [
                'projectId' => $this->changeOrder->bco_project_id,
                'id' => $this->changeOrder->bco_id
            ]));
        } else {
            $message->line("**PO Number:** {$this->changeOrder->purchaseOrder->porder_no}");
            $message->line("**Previous Total:** $" . number_format($this->changeOrder->poco_previous_total, 2));
            $message->line("**New Total:** $" . number_format($this->changeOrder->poco_new_total, 2));
            $message->line("**Change:** $" . number_format($this->changeOrder->poco_new_total - $this->changeOrder->poco_previous_total, 2));
            $message->action('View PCO', route('admin.po-change-orders.show', $this->changeOrder->poco_id));
        }

        if (!empty($this->changeOrder->bco_reason ?? $this->changeOrder->poco_reason)) {
            $reason = $isBCO ? $this->changeOrder->bco_reason : $this->changeOrder->poco_reason;
            $message->line("**Reason:** {$reason}");
        }

        return $message;
    }

    public function toArray($notifiable)
    {
        $isBCO = $this->type === 'bco';
        
        return [
            'type' => 'change_order',
            'change_order_type' => $this->type,
            'action' => $this->action,
            'actor_name' => $this->actorName,
            'number' => $isBCO ? $this->changeOrder->bco_number : $this->changeOrder->poco_number,
            'change_order_id' => $isBCO ? $this->changeOrder->bco_id : $this->changeOrder->poco_id,
            'previous_amount' => $isBCO ? $this->changeOrder->bco_previous_amount : $this->changeOrder->poco_previous_total,
            'new_amount' => $isBCO ? $this->changeOrder->bco_new_amount : $this->changeOrder->poco_new_total,
        ];
    }
}
