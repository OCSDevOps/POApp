<?php

namespace App\Jobs;

use App\Models\Budget;
use App\Models\ProjectRole;
use App\Models\User;
use App\Notifications\BudgetWarningNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendBudgetWarningNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(
        public int $budgetId,
        public float $utilizationPercent,
        public int $threshold
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $budget = Budget::with(['project', 'costCode'])->find($this->budgetId);
        if (!$budget || !$budget->project || !$budget->costCode) {
            return;
        }

        $projectRoles = ProjectRole::where('project_id', $budget->budget_project_id)
            ->whereIn('role_name', ['project_manager', 'finance', 'executive', 'director'])
            ->where('can_approve_po', true)
            ->get();

        $recipientUserIds = $projectRoles->pluck('user_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique();

        if ($budget->project->proj_createby) {
            $recipientUserIds->push((int) $budget->project->proj_createby);
        }

        $users = User::whereIn('id', $recipientUserIds->filter()->unique()->values()->all())->get();
        if ($users->isEmpty()) {
            return;
        }

        Notification::sendNow($users, new BudgetWarningNotification(
            $budget->project,
            $budget->costCode,
            $budget,
            $this->utilizationPercent,
            $this->threshold
        ));

        Log::info('Queued budget warning notifications delivered', [
            'budget_id' => $this->budgetId,
            'project_id' => $budget->budget_project_id,
            'threshold' => $this->threshold,
            'recipients' => $users->count(),
        ]);
    }
}
