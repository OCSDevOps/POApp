<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BackorderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $poNumber;
    protected $projectName;
    protected $supplierName;
    protected $remainingItems;

    /**
    * @param array $context ['po_no'=>..., 'project'=>..., 'supplier'=>..., 'remaining_items'=>[['item_code'=>..., 'remaining_qty'=>...], ...]]
    */
    public function __construct(array $context)
    {
        $this->poNumber = $context['po_no'] ?? '';
        $this->projectName = $context['project'] ?? '';
        $this->supplierName = $context['supplier'] ?? '';
        $this->remainingItems = $context['remaining_items'] ?? [];
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'po_no' => $this->poNumber,
            'project' => $this->projectName,
            'supplier' => $this->supplierName,
            'remaining_items' => $this->remainingItems,
            'message' => "Backorder detected for PO {$this->poNumber} ({$this->projectName}) with supplier {$this->supplierName}.",
        ];
    }
}
