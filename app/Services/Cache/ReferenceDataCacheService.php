<?php

namespace App\Services\Cache;

use App\Models\CostCode;
use App\Models\Project;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReferenceDataCacheService
{
    private const TTL_SECONDS = 900; // 15 minutes

    /**
     * Get active projects for current company.
     */
    public function getActiveProjects(?int $companyId = null): Collection
    {
        $companyId = $companyId ?? (int) session('company_id');
        return $this->remember("active_projects:{$companyId}", function () {
            return Project::active()->orderByName()->get();
        });
    }

    /**
     * Get active suppliers for current company.
     */
    public function getActiveSuppliers(?int $companyId = null): Collection
    {
        $companyId = $companyId ?? (int) session('company_id');
        return $this->remember("active_suppliers:{$companyId}", function () {
            return Supplier::active()->orderByName()->get();
        });
    }

    /**
     * Get active cost codes for current company.
     */
    public function getActiveCostCodes(?int $companyId = null): Collection
    {
        $companyId = $companyId ?? (int) session('company_id');
        return $this->remember("active_cost_codes:{$companyId}", function () {
            return CostCode::active()->orderBy('cc_no')->get();
        });
    }

    /**
     * Project list used by budget reports.
     */
    public function getReportProjects(?int $companyId = null): Collection
    {
        $companyId = $companyId ?? (int) session('company_id');
        return $this->remember("report_projects:{$companyId}", function () use ($companyId) {
            return DB::table('project_master')
                ->where('company_id', $companyId)
                ->where('proj_status', 1)
                ->select('proj_id', 'proj_name', 'proj_number')
                ->orderBy('proj_name')
                ->get();
        });
    }

    /**
     * Clear known reference caches for a company.
     */
    public function clearCompanyReferenceCaches(?int $companyId = null): void
    {
        $companyId = $companyId ?? (int) session('company_id');
        $keys = [
            "active_projects:{$companyId}",
            "active_suppliers:{$companyId}",
            "active_cost_codes:{$companyId}",
            "report_projects:{$companyId}",
        ];

        foreach ($keys as $key) {
            Cache::forget($this->key($key));
        }
    }

    /**
     * Shared remember wrapper with project cache prefix.
     */
    private function remember(string $key, \Closure $callback): Collection
    {
        return Cache::remember(
            $this->key($key),
            now()->addSeconds(self::TTL_SECONDS),
            $callback
        );
    }

    /**
     * Namespace keys to avoid collisions.
     */
    private function key(string $rawKey): string
    {
        return 'poapp:ref:' . $rawKey;
    }
}
