<?php

namespace App\Jobs;

use App\Models\ReportExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateBudgetVsActualExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 300;

    public function __construct(public int $reportExportId)
    {
        $this->onQueue('reports');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $export = ReportExport::find($this->reportExportId);
        if (!$export) {
            return;
        }

        $export->status = 'processing';
        $export->started_at = now();
        $export->updated_at = now();
        $export->save();

        try {
            $params = $export->parameters_decoded;
            $projectId = (int) ($params['project_id'] ?? 0);
            $companyId = (int) ($export->company_id ?? 0);

            if ($projectId <= 0 || $companyId <= 0) {
                throw new \RuntimeException('Invalid report export parameters.');
            }

            $project = DB::table('project_master')
                ->where('proj_id', $projectId)
                ->where('company_id', $companyId)
                ->select('proj_id', 'proj_name', 'proj_number')
                ->first();

            if (!$project) {
                throw new \RuntimeException('Project not found for report export.');
            }

            $reportData = DB::table('budget_master as b')
                ->join('cost_code_master as cc', 'b.budget_cost_code_id', '=', 'cc.cc_id')
                ->where('b.budget_project_id', $projectId)
                ->where('b.company_id', $companyId)
                ->select(
                    'cc.cc_id',
                    'cc.cc_no as cost_code',
                    'cc.cc_description as cost_code_name',
                    'cc.parent_code',
                    'cc.level',
                    'b.budget_original_amount as original',
                    'b.budget_revised_amount as revised',
                    'b.committed',
                    'b.actual',
                    'b.variance',
                    DB::raw('(b.committed + b.actual) as total_spent'),
                    DB::raw('CASE 
                        WHEN b.budget_revised_amount > 0 
                        THEN ((b.committed + b.actual) / b.budget_revised_amount * 100) 
                        ELSE 0 
                    END as utilization_pct')
                )
                ->orderBy('cc.cc_no')
                ->get();

            $summary = [
                'total_original' => $reportData->sum('original'),
                'total_revised' => $reportData->sum('revised'),
                'total_committed' => $reportData->sum('committed'),
                'total_actual' => $reportData->sum('actual'),
            ];
            $summary['total_spent'] = $summary['total_committed'] + $summary['total_actual'];
            $summary['total_remaining'] = $summary['total_revised'] - $summary['total_spent'];
            $summary['overall_utilization'] = $summary['total_revised'] > 0
                ? ($summary['total_spent'] / $summary['total_revised'] * 100)
                : 0;

            $csvContent = $this->buildCsv($project, $reportData, $summary);

            $fileName = 'budget_vs_actual_' . str_replace(' ', '_', $project->proj_number) . '_' . now()->format('Ymd_His') . '.csv';
            $filePath = 'reports/budget/' . $companyId . '/' . $fileName;

            Storage::disk('local')->put($filePath, $csvContent);

            $export->status = 'completed';
            $export->file_name = $fileName;
            $export->file_path = $filePath;
            $export->error_message = null;
            $export->completed_at = now();
            $export->updated_at = now();
            $export->save();
        } catch (\Throwable $e) {
            $export->status = 'failed';
            $export->error_message = substr($e->getMessage(), 0, 2000);
            $export->updated_at = now();
            $export->save();

            Log::error('Budget report export job failed', [
                'report_export_id' => $this->reportExportId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Build CSV payload for budget report.
     */
    private function buildCsv(object $project, $reportData, array $summary): string
    {
        $stream = fopen('php://temp', 'w+');

        fprintf($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($stream, ['Budget vs Actual Report']);
        fputcsv($stream, ['Project:', $project->proj_name . ' (' . $project->proj_number . ')']);
        fputcsv($stream, ['Generated:', now()->format('Y-m-d H:i:s')]);
        fputcsv($stream, []);

        fputcsv($stream, ['Summary']);
        fputcsv($stream, ['Total Original Budget:', '$' . number_format($summary['total_original'], 2)]);
        fputcsv($stream, ['Total Revised Budget:', '$' . number_format($summary['total_revised'], 2)]);
        fputcsv($stream, ['Total Committed:', '$' . number_format($summary['total_committed'], 2)]);
        fputcsv($stream, ['Total Actual:', '$' . number_format($summary['total_actual'], 2)]);
        fputcsv($stream, ['Total Spent:', '$' . number_format($summary['total_spent'], 2)]);
        fputcsv($stream, ['Total Remaining:', '$' . number_format($summary['total_remaining'], 2)]);
        fputcsv($stream, ['Overall Utilization:', number_format($summary['overall_utilization'], 2) . '%']);
        fputcsv($stream, []);

        fputcsv($stream, [
            'Cost Code',
            'Description',
            'Level',
            'Original Budget',
            'Revised Budget',
            'Committed',
            'Actual',
            'Total Spent',
            'Variance',
            'Utilization %',
            'Status',
        ]);

        foreach ($reportData as $row) {
            $status = 'On Track';
            if ($row->utilization_pct >= 100) {
                $status = 'Over Budget';
            } elseif ($row->utilization_pct >= 90) {
                $status = 'Critical';
            } elseif ($row->utilization_pct >= 75) {
                $status = 'At Risk';
            }

            fputcsv($stream, [
                $row->cost_code,
                $row->cost_code_name,
                $row->level,
                $row->original,
                $row->revised,
                $row->committed,
                $row->actual,
                $row->total_spent,
                $row->variance,
                number_format($row->utilization_pct, 2) . '%',
                $status,
            ]);
        }

        rewind($stream);
        $csv = stream_get_contents($stream);
        fclose($stream);

        return (string) $csv;
    }
}
