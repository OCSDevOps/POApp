<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $project;
    protected $costCode;
    protected $budget;
    protected $utilizationPercent;
    protected $threshold;

    public function __construct($project, $costCode, $budget, $utilizationPercent, $threshold)
    {
        $this->project = $project;
        $this->costCode = $costCode;
        $this->budget = $budget;
        $this->utilizationPercent = $utilizationPercent;
        $this->threshold = $threshold;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $urgency = $this->utilizationPercent >= 90 ? 'Critical' : 'Warning';
        $color = $this->utilizationPercent >= 90 ? '#dc3545' : '#ffc107';

        return (new MailMessage)
            ->subject("Budget {$urgency}: {$this->project->proj_name} - {$this->costCode->cc_no}")
            ->greeting("Budget {$urgency} Alert")
            ->line("A budget threshold has been reached for project **{$this->project->proj_name}**.")
            ->line("**Cost Code:** {$this->costCode->cc_no} - {$this->costCode->cc_description}")
            ->line("**Original Budget:** $" . number_format($this->budget->original_amount, 2))
            ->line("**Committed:** $" . number_format($this->budget->committed, 2))
            ->line("**Actual:** $" . number_format($this->budget->actual, 2))
            ->line("**Utilization:** {$this->utilizationPercent}% (Threshold: {$this->threshold}%)")
            ->action('View Budget Details', route('admin.budgets.view', $this->project->proj_id))
            ->line('Please review and take appropriate action to ensure budget compliance.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'budget_warning',
            'threshold' => $this->threshold,
            'utilization' => $this->utilizationPercent,
            'project_id' => $this->project->proj_id,
            'project_name' => $this->project->proj_name,
            'cost_code' => $this->costCode->cc_no,
            'cost_code_description' => $this->costCode->cc_description,
            'budget_amount' => $this->budget->original_amount,
            'committed' => $this->budget->committed,
            'actual' => $this->budget->actual,
        ];
    }
}
