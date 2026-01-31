<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class BudgetReportController extends Controller
{
    protected $exportService;
    
    public function __construct(ReportExportService $exportService)
    {
        $this->exportService = $exportService;
    }
    /**
     * Display budget vs actual report with project selection
     */
    public function index(Request $request)
    {
        // Get all projects for the current company
        $companyId = Session::get('company_id', 1);
        
        $projects = DB::table('project_master')
            ->where('proj_company_id', $companyId)
            ->where('proj_status', 1)
            ->select('proj_id', 'proj_name', 'proj_no')
            ->orderBy('proj_name')
            ->get();
        
        $selectedProjectId = $request->get('project_id');
        $reportData = null;
        $summary = null;
        
        if ($selectedProjectId) {
            $reportData = $this->getBudgetVsActualData($selectedProjectId);
            $summary = $this->calculateSummary($reportData);
        }
        
        return view('admin.reports.budget-vs-actual', compact('projects', 'selectedProjectId', 'reportData', 'summary'));
    }
    
    /**
     * Get budget vs actual data for a project
     */
    private function getBudgetVsActualData($projectId)
    {
        return DB::table('budget_master as b')
            ->join('cost_code_master as cc', 'b.budget_cost_code_id', '=', 'cc.cc_id')
            ->where('b.budget_project_id', $projectId)
            ->select(
                'cc.cc_id',
                'cc.cc_no as cost_code',
                'cc.cc_name as cost_code_name',
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
                END as utilization_pct'),
                DB::raw('CASE
                    WHEN ((b.committed + b.actual) / b.budget_revised_amount * 100) >= 90 THEN \'danger\'
                    WHEN ((b.committed + b.actual) / b.budget_revised_amount * 100) >= 75 THEN \'warning\'
                    ELSE \'success\'
                END as status_level')
            )
            ->orderBy('cc.cc_no')
            ->get();
    }
    
    /**
     * Calculate summary totals
     */
    private function calculateSummary($reportData)
    {
        if (!$reportData || $reportData->isEmpty()) {
            return null;
        }
        
        $totalOriginal = $reportData->sum('original');
        $totalRevised = $reportData->sum('revised');
        $totalCommitted = $reportData->sum('committed');
        $totalActual = $reportData->sum('actual');
        $totalSpent = $totalCommitted + $totalActual;
        $totalVariance = $reportData->sum('variance');
        $overUtilized = $reportData->where('utilization_pct', '>=', 100)->count();
        $atRisk = $reportData->whereBetween('utilization_pct', [75, 99.99])->count();
        
        return [
            'total_original' => $totalOriginal,
            'total_revised' => $totalRevised,
            'total_committed' => $totalCommitted,
            'total_actual' => $totalActual,
            'total_spent' => $totalSpent,
            'total_variance' => $totalVariance,
            'total_remaining' => $totalRevised - $totalSpent,
            'overall_utilization' => $totalRevised > 0 ? ($totalSpent / $totalRevised * 100) : 0,
            'over_budget_count' => $overUtilized,
            'at_risk_count' => $atRisk,
            'on_track_count' => $reportData->where('utilization_pct', '<', 75)->count(),
        ];
    }
    
    /**
     * Get drill-down details for a specific cost code
     */
    public function drilldown(Request $request, $projectId, $costCodeId)
    {
        $companyId = Session::get('company_id', 1);
        
        $costCode = DB::table('cost_code_master')
            ->where('cc_id', $costCodeId)
            ->where('company_id', $companyId)
            ->first();
        
        // Get all purchase orders for this cost code (company-scoped)
        $purchaseOrders = DB::table('purchase_order_master as pom')
            ->join('purchase_order_details as pod', 'pom.porder_id', '=', 'pod.po_detail_porder_ms')
            ->where('pom.porder_project_ms', $projectId)
            ->where('pom.company_id', $companyId)
            ->where('pod.company_id', $companyId)
            ->where('pod.po_detail_ccode', $costCodeId)
            ->select(
                'pom.porder_id',
                'pom.porder_no',
                'pom.porder_date',
                DB::raw('SUM(pod.po_detail_unitprice * pod.po_detail_quantity) as po_amount'),
                'pom.porder_general_status as porder_status'
            )
            ->groupBy('pom.porder_id', 'pom.porder_no', 'pom.porder_date', 'pom.porder_general_status')
            ->orderBy('pom.porder_date', 'desc')
            ->get();
        
        // Get all receive orders for this cost code (company-scoped)
        $receiveOrders = DB::table('receive_order_master as rom')
            ->join('receive_order_details as rod', 'rom.rorder_id', '=', 'rod.ro_detail_rorder_ms')
            ->where('rom.rorder_project_ms', $projectId)
            ->where('rom.company_id', $companyId)
            ->where('rod.company_id', $companyId)
            ->where('rod.ro_detail_ccode', $costCodeId)
            ->select(
                'rom.rorder_id',
                'rom.rorder_slip_no as ro_number',
                'rom.rorder_date as ro_date',
                DB::raw('SUM(rod.ro_detail_quantity * rod.ro_detail_unitprice) as ro_amount'),
                'rom.rorder_status as ro_status'
            )
            ->groupBy('rom.rorder_id', 'rom.rorder_slip_no', 'rom.rorder_date', 'rom.rorder_status')
            ->orderBy('rom.rorder_date', 'desc')
            ->get();
        
        return view('admin.reports.budget-drilldown', compact('costCode', 'purchaseOrders', 'receiveOrders'));
    }
    
    /**
     * Variance Analysis Dashboard
     */
    public function varianceAnalysis(Request $request)
    {
        $companyId = Session::get('company_id', 1);
        
        $projects = DB::table('project_master')
            ->where('proj_company_id', $companyId)
            ->where('proj_status', 1)
            ->select('proj_id', 'proj_name')
            ->get();
        
        $selectedProjectId = $request->get('project_id');
        
        if (!$selectedProjectId) {
            $selectedProjectId = $projects->first()->proj_id ?? null;
        }
        
        $topVariances = null;
        $utilizationChart = null;
        $alertsData = null;
        
        if ($selectedProjectId) {
            // Get top 10 variances (positive and negative)
            $topVariances = DB::table('budget_master as b')
                ->join('cost_code_master as cc', 'b.budget_cost_code_id', '=', 'cc.cc_id')
                ->where('b.budget_project_id', $selectedProjectId)
                ->select(
                    'cc.cc_no',
                    'cc.cc_name',
                    'b.budget_revised_amount',
                    'b.committed',
                    'b.actual',
                    'b.variance',
                    DB::raw('CASE 
                        WHEN b.variance < 0 THEN \'over\'
                        WHEN b.variance = 0 THEN \'exact\'
                        ELSE \'under\'
                    END as variance_status')
                )
                ->orderBy(DB::raw('ABS(b.variance)'), 'desc')
                ->limit(10)
                ->get();
            
            // Get utilization distribution for chart
            $utilizationChart = DB::table('budget_master as b')
                ->where('b.budget_project_id', $selectedProjectId)
                ->select(
                    DB::raw('COUNT(CASE WHEN ((b.committed + b.actual) / b.budget_revised_amount * 100) < 50 THEN 1 END) as under_50'),
                    DB::raw('COUNT(CASE WHEN ((b.committed + b.actual) / b.budget_revised_amount * 100) BETWEEN 50 AND 74.99 THEN 1 END) as pct_50_74'),
                    DB::raw('COUNT(CASE WHEN ((b.committed + b.actual) / b.budget_revised_amount * 100) BETWEEN 75 AND 89.99 THEN 1 END) as pct_75_89'),
                    DB::raw('COUNT(CASE WHEN ((b.committed + b.actual) / b.budget_revised_amount * 100) BETWEEN 90 AND 99.99 THEN 1 END) as pct_90_99'),
                    DB::raw('COUNT(CASE WHEN ((b.committed + b.actual) / b.budget_revised_amount * 100) >= 100 THEN 1 END) as over_100')
                )
                ->first();
            
            // Get budget alerts
            $alertsData = DB::table('budget_master as b')
                ->join('cost_code_master as cc', 'b.budget_cost_code_id', '=', 'cc.cc_id')
                ->where('b.budget_project_id', $selectedProjectId)
                ->where(function($query) {
                    $query->where('b.warning_notification_sent', 1)
                          ->orWhere('b.critical_notification_sent', 1)
                          ->orWhere(DB::raw('b.variance'), '<', 0);
                })
                ->select(
                    'cc.cc_no',
                    'cc.cc_name',
                    'b.budget_revised_amount',
                    'b.committed',
                    'b.actual',
                    'b.variance',
                    'b.warning_notification_sent',
                    'b.critical_notification_sent',
                    DB::raw('((b.committed + b.actual) / b.budget_revised_amount * 100) as utilization')
                )
                ->orderBy('utilization', 'desc')
                ->get();
        }
        
        return view('admin.reports.variance-analysis', compact(
            'projects', 
            'selectedProjectId', 
            'topVariances', 
            'utilizationChart',
            'alertsData'
        ));
    }
    
    /**
     * Export budget vs actual report to CSV/Excel
     */
    public function export(Request $request)
    {
        $companyId = Session::get('company_id', 1);
        $projectId = $request->get('project_id');
        $format = $request->get('format', 'csv');
        
        if (!$projectId) {
            return redirect()->route('admin.reports.budget-vs-actual')
                ->with('error', 'Please select a project to export.');
        }
        
        // Get project info (company-scoped)
        $project = DB::table('project_master')
            ->where('proj_id', $projectId)
            ->where('proj_company_id', $companyId)
            ->select('proj_name', 'proj_no')
            ->first();
        
        if (!$project) {
            return redirect()->route('admin.reports.budget-vs-actual')
                ->with('error', 'Project not found.');
        }
        
        // Get report data
        $reportData = $this->getBudgetVsActualData($projectId);
        $summary = $this->calculateSummary($reportData);
        
        // Generate filename
        $filename = 'budget_report_' . str_replace(' ', '_', $project->proj_no) . '_' . now()->format('Y-m-d') . '.csv';
        
        // Generate CSV content
        ob_start();
        $output = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Project info header
        fputcsv($output, ['Budget vs Actual Report']);
        fputcsv($output, ['Project:', $project->proj_name . ' (' . $project->proj_no . ')']);
        fputcsv($output, ['Generated:', now()->format('Y-m-d H:i:s')]);
        fputcsv($output, []); // Empty row
        
        // Summary section
        if ($summary) {
            fputcsv($output, ['Summary']);
            fputcsv($output, ['Total Original Budget:', '$' . number_format($summary['total_original'], 2)]);
            fputcsv($output, ['Total Revised Budget:', '$' . number_format($summary['total_revised'], 2)]);
            fputcsv($output, ['Total Committed:', '$' . number_format($summary['total_committed'], 2)]);
            fputcsv($output, ['Total Actual:', '$' . number_format($summary['total_actual'], 2)]);
            fputcsv($output, ['Total Spent:', '$' . number_format($summary['total_spent'], 2)]);
            fputcsv($output, ['Total Remaining:', '$' . number_format($summary['total_remaining'], 2)]);
            fputcsv($output, ['Overall Utilization:', number_format($summary['overall_utilization'], 2) . '%']);
            fputcsv($output, []); // Empty row
        }
        
        // Data headers
        fputcsv($output, [
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
            'Status'
        ]);
        
        // Data rows
        foreach ($reportData as $row) {
            $status = 'On Track';
            if ($row->utilization_pct >= 100) {
                $status = 'Over Budget';
            } elseif ($row->utilization_pct >= 90) {
                $status = 'Critical';
            } elseif ($row->utilization_pct >= 75) {
                $status = 'At Risk';
            }
            
            fputcsv($output, [
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
                $status
            ]);
        }
        
        fclose($output);
        $csvContent = ob_get_clean();
        
        // If PDF format requested, return print-friendly view
        if ($format === 'pdf') {
            return view('admin.reports.budget-vs-actual-pdf', compact('project', 'reportData', 'summary'));
        }
        
        // Otherwise return CSV download
        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
