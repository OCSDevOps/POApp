<?php

namespace App\Providers;

use App\Services\Security\AuditLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.admin', function ($view) {
            $currentCompany = null;
            $switchableCompanies = collect();

            if (session('u_type') == 1 && session('company_id')) {
                $currentCompany = \App\Models\Company::find(session('company_id'));
                $switchableCompanies = \App\Models\Company::where('status', 1)
                    ->orderBy('name')
                    ->get();
            }

            $view->with('currentCompany', $currentCompany);
            $view->with('switchableCompanies', $switchableCompanies);
        });

        $this->registerGlobalAuditListeners();
    }

    /**
     * Register Eloquent event listeners for audit trails.
     */
    private function registerGlobalAuditListeners(): void
    {
        Event::listen('eloquent.created: *', function ($eventName, array $data) {
            $this->logModelAuditEvent('created', $data);
        });

        Event::listen('eloquent.updated: *', function ($eventName, array $data) {
            $this->logModelAuditEvent('updated', $data);
        });

        Event::listen('eloquent.deleted: *', function ($eventName, array $data) {
            $this->logModelAuditEvent('deleted', $data);
        });
    }

    /**
     * Convert Eloquent model events into audit log records.
     */
    private function logModelAuditEvent(string $eventName, array $data): void
    {
        $model = $data[0] ?? null;
        if (!$model instanceof Model) {
            return;
        }

        if ($model->getTable() === 'audit_logs') {
            return;
        }

        try {
            /** @var AuditLogService $auditLogService */
            $auditLogService = app(AuditLogService::class);

            if ($eventName === 'created') {
                $auditLogService->logModelEvent('created', $model, [], $model->getAttributes());
                return;
            }

            if ($eventName === 'updated') {
                $changes = $model->getChanges();
                unset($changes['updated_at']);

                if (empty($changes)) {
                    return;
                }

                $original = [];
                foreach (array_keys($changes) as $changedField) {
                    $original[$changedField] = $model->getOriginal($changedField);
                }

                $auditLogService->logModelEvent('updated', $model, $original, $changes);
                return;
            }

            if ($eventName === 'deleted') {
                $auditLogService->logModelEvent('deleted', $model, $model->getOriginal(), []);
            }
        } catch (\Throwable $e) {
            Log::warning('Audit log model event failed', [
                'event' => $eventName,
                'model' => get_class($model),
                'message' => $e->getMessage(),
            ]);
        }
    }
}
