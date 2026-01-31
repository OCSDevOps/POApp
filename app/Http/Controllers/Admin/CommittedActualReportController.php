<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CommittedActualReportController extends Controller
{
    /**
     * Display committed vs actual timeline report
     */
    public function index(Request $request)
    {
        $companyId = Session::get('company_id', 1);
        
        // Get filter parameters
        $projectId = $request->get('project_id');
        $dateRange = $request->get('date_range', '6months'); // 3months, 6months, 1year, all
        
        // Calculate date range
        $endDate = now();
        switch ($dateRange) {
            case '3months':
                $startDate = now()->subMonths(3);
                break;
            case '1year':
                $startDate = now()->subYear();
                break;
            case 'all':
                $startDate = now()->subYears(5);
                break;
            case '6months':
            default:
                $startDate = now()->subMonths(6);
                break;
        }
        
        // Get projects for dropdown
        $projects = DB::table('project_master')
            ->where('proj_company_id', $companyId)
            ->where('proj_status', 1)
            ->select('proj_id', 'proj_name', 'proj_no')
            ->orderBy('proj_name')
            ->get();
        
        $timelineData = null;
        $summary = null;
        $costCodeBreakdown = null;
        
        if ($projectId) {
            $timelineData = $this->getTimelineData($projectId, $startDate, $endDate);
            $summary = $this->calculateSummary($projectId, $startDate, $endDate);
            $costCodeBreakdown = $this->getCostCodeBreakdown($projectId, $startDate, $endDate);
        }
        
        return view('admin.reports.committed-vs-actual', compact(
            'projects',
            'projectId',
            'dateRange',
            'startDate',
            'endDate',
            'timelineData',
            'summary',
            'costCodeBreakdown'
        ));
    }
    
    /**
     * Get timeline data for chart
     */
    private function getTimelineData($projectId, $startDate, $endDate)
    {
        // Get monthly committed (PO) data
        $committedData = DB::table('purchase_order_master as pom')
            ->join('purchase_order_details as pod', 'pom.porder_id', '=', 'pod.pod_porder_id')
            ->where('pom.porder_project_ms', $projectId)
            ->where('pom.porder_status', '>=', 2) // Approved POs only
            ->whereBetween('pom.porder_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select(
                DB::raw("FORMAT(pom.porder_date, 'yyyy-MM') as month"),
                DB::raw('SUM(pod.pod_price * pod.pod_qty) as amount')
            )
            ->groupBy(DB::raw("FORMAT(pom.porder_date, 'yyyy-MM')"))
            ->orderBy(DB::raw("FORMAT(pom.porder_date, 'yyyy-MM')"))
            ->get()
            ->keyBy('month');
        
        // Get monthly actual (Receive Order) data
        $actualData = DB::table('receive_order_master as rom')
            ->join('receive_order_items as roi', 'rom.ro_id', '=', 'roi.roi_ro_id')
            ->where('rom.ro_project_id', $projectId)
            ->where('rom.ro_status', '>=', 2) // Completed ROs only
            ->whereBetween('rom.ro_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select(
                DB::raw("FORMAT(rom.ro_date, 'yyyy-MM') as month"),
                DB::raw('SUM(roi.roi_received_qty * roi.roi_unit_price) as amount')
            )
            ->groupBy(DB::raw("FORMAT(rom.ro_date, 'yyyy-MM')"))
            ->orderBy(DB::raw("FORMAT(rom.ro_date, 'yyyy-MM')"))
            ->get()
            ->keyBy('month');
        
        // Generate all months in range
        $labels = [];
        $committed = [];
        $actual = [];
        
        $current = clone $startDate;
        while ($current <= $endDate) {
            $monthKey = $current->format('Y-m');
            $labels[] = $current->format('M Y');
            $committed[] = $committedData->has($monthKey) ? (float) $committedData[$monthKey]->amount : 0;
            $actual[] = $actualData->has($monthKey) ? (float) $actualData[$monthKey]->amount : 0;
            $current->addMonth();
        }
        
        return [
            'labels' => $labels,
            'committed' => $committed,
            'actual' => $actual,
        ];
    }
    
    /**
     * Calculate summary statistics
     */
    private function calculateSummary($projectId, $startDate, $endDate)
    {
        // Total budget for project
        $totalBudget = DB::table('budget_master')
            ->where('budget_project_id', $projectId)
            ->sum('budget_revised_amount');
        
        // Total committed in period
        $totalCommitted = DB::table('purchase_order_master as pom')
            ->join('purchase_order_details as pod', 'pom.porder_id', '=', 'pod.pod_porder_id')
            ->where('pom.porder_project_ms', $projectId)
            ->where('pom.porder_status', '>=', 2)
            ->whereBetween('pom.porder_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum(DB::raw('pod.pod_price * pod.pod_qty'));
        
        // Total actual in period
        $totalActual = DB::table('receive_order_master as rom')
            ->join('receive_order_items as roi', 'rom.ro_id', '=', 'roi.roi_ro_id')
            ->where('rom.ro_project_id', $projectId)
            ->where('rom.ro_status', '>=', 2)
            ->whereBetween('rom.ro_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum(DB::raw('roi.roi_received_qty * roi.roi_unit_price'));
        
        // Cumulative totals (all time)
        $cumulativeCommitted = DB::table('purchase_order_master as pom')
            ->join('purchase_order_details as pod', 'pom.porder_id', '=', 'pod.pod_porder_id')
            ->where('pom.porder_project_ms', $projectId)
            ->where('pom.porder_status', '>=', 2)
            ->sum(DB::raw('pod.pod_price * pod.pod_qty'));
        
        $cumulativeActual = DB::table('receive_order_master as rom')
            ->join('receive_order_items as roi', 'rom.ro_id', '=', 'roi.roi_ro_id')
            ->where('rom.ro_project_id', $projectId)
            ->where('rom.ro_status', '>=', 2)
            ->sum(DB::raw('roi.roi_received_qty * roi.roi_unit_price'));
        
        // PO Count
        $poCount = DB::table('purchase_order_master')
            ->where('porder_project_ms', $projectId)
            ->where('porder_status', '>=', 2)
            ->whereBetween('porder_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->count();
        
        // RO Count
        $roCount = DB::table('receive_order_master')
            ->where('ro_project_id', $projectId)
            ->where('ro_status', '>=', 2)
            ->whereBetween('ro_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->count();
        
        return [
            'total_budget' => $totalBudget,
            'period_committed' => $totalCommitted,
            'period_actual' => $totalActual,
            'cumulative_committed' => $cumulativeCommitted,
            'cumulative_actual' => $cumulativeActual,
            'remaining_budget' => $totalBudget - $cumulativeCommitted - $cumulativeActual,
            'utilization_rate' => $totalBudget > 0 ? (($cumulativeCommitted + $cumulativeActual) / $totalBudget * 100) : 0,
            'po_count' => $poCount,
            'ro_count' => $roCount,
            'variance' => $totalCommitted - $totalActual,
        ];
    }
    
    /**
     * Get cost code breakdown
     */
    private function getCostCodeBreakdown($projectId, $startDate, $endDate)
    {
        return DB::table('budget_master as b')
            ->join('cost_code_master as cc', 'b.budget_cost_code_id', '=', 'cc.cc_id')
            ->leftJoin('purchase_order_details as pod', function($join) use ($projectId, $startDate, $endDate) {
                $join->on('b.budget_cost_code_id', '=', 'pod.pod_cost_code')
                    ->join('purchase_order_master as pom', 'pod.pod_porder_id', '=', 'pom.porder_id')
                    ->where('pom.porder_project_ms', $projectId)
                    ->where('pom.porder_status', '>=', 2)
                    ->whereBetween('pom.porder_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })
            ->leftJoin('receive_order_items as roi', function($join) use ($projectId, $startDate, $endDate) {
                $join->on('b.budget_cost_code_id', '=', 'roi.roi_po_item_id')
                    ->join('receive_order_master as rom', 'roi.roi_ro_id', '=', 'rom.ro_id')
                    ->where('rom.ro_project_id', $projectId)
                    ->where('rom.ro_status', '>=', 2)
                    ->whereBetween('rom.ro_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })
            ->where('b.budget_project_id', $projectId)
            ->select(
                'cc.cc_no',
                'cc.cc_name',
                'b.budget_revised_amount as budget',
                DB::raw('SUM(DISTINCT pod.pod_price * pod.pod_qty) as committed'),
                DB::raw('SUM(DISTINCT roi.roi_received_qty * roi.roi_unit_price) as actual')
            )
            ->groupBy('cc.cc_no', 'cc.cc_name', 'b.budget_revised_amount')
            ->orderBy('cc.cc_no')
            ->get();
    }
    
    /**
     * Export committed vs actual report
     */
    public function export(Request $request)
    {
        $projectId = $request->get('project_id');
        $dateRange = $request->get('date_range', '6months');
        
        if (!$projectId) {
            return redirect()->route('admin.reports.committed-vs-actual')
                ->with('error', 'Please select a project to export.');
        }
        
        // Calculate date range
        $endDate = now();
        switch ($dateRange) {
            case '3months':
                $startDate = now()->subMonths(3);
                break;
            case '1year':
                $startDate = now()->subYear();
                break;
            case 'all':
                $startDate = now()->subYears(5);
                break;
            default:
                $startDate = now()->subMonths(6);
        }
        
        // Get project info
        $project = DB::table('project_master')
            ->where('proj_id', $projectId)
            ->select('proj_name', 'proj_no')
            ->first();
        
        if (!$project) {
            return redirect()->route('admin.reports.committed-vs-actual')
                ->with('error', 'Project not found.');
        }
        
        $timelineData = $this->getTimelineData($projectId, $startDate, $endDate);
        $summary = $this->calculateSummary($projectId, $startDate, $endDate);
        $costCodeBreakdown = $this->getCostCodeBreakdown($projectId, $startDate, $endDate);
        
        $filename = 'committed_vs_actual_' . str_replace(' ', '_', $project->proj_no) . '_' . now()->format('Y-m-d') . '.csv';
        
        // Generate CSV
        ob_start();
        $output = fopen('php://output', 'w');
        
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        fputcsv($output, ['Committed vs Actual Report']);
        fputcsv($output, ['Project:', $project->proj_name . ' (' . $project->proj_no . ')']);
        fputcsv($output, ['Period:', $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y')]);
        fputcsv($output, ['Generated:', now()->format('Y-m-d H:i:s')]);
        fputcsv($output, []);
        
        // Summary
        if ($summary) {
            fputcsv($output, ['Summary']);
            fputcsv($output, ['Total Budget:', '$' . number_format($summary['total_budget'], 2)]);
            fputcsv($output, ['Period Committed:', '$' . number_format($summary['period_committed'], 2)];
            fputcsv($output, ['Period Actual:', '$' . number_format($summary['period_actual'], 2)];
            fputcsv($output, ['Cumulative Committed:', '$' . number_format($summary['cumulative_committed'], 2)];
            fputcsv($output, ['Cumulative Actual:', '$' . number_format($summary['cumulative_actual'], 2)]);
            fputcsv($output, ['Remaining Budget:', '$' . number_format($summary['remaining_budget'], 2)]);
            fputcsv($output, ['Utilization Rate:', number_format($summary['utilization_rate'], 2) . '%']);
            fputcsv($output, ['PO Count:', $summary['po_count']]);
            fputcsv($output, ['RO Count:', $summary['ro_count']]);
            fputcsv($output, []);
        }
        
        // Monthly Timeline
        fputcsv($output, ['Monthly Timeline']);
        fputcsv($output, ['Month', 'Committed', 'Actual', 'Variance']);
        foreach ($timelineData['labels'] as $index => $month) {
            fputcsv($output, [
                $month,
                $timelineData['committed'][$index],
                $timelineData['actual'][$index],
                $timelineData['committed'][$index] - $timelineData['actual'][$index]
            ]);
        }
        fputcsv($output, []);
        
        // Cost Code Breakdown
        fputcsv($output, ['Cost Code Breakdown']);
        fputcsv($output, ['Cost Code', 'Description', 'Budget', 'Committed', 'Actual', 'Variance', 'Utilization %']);
        foreach ($costCodeBreakdown as $row) {
            $variance = $row->budget - ($row->committed + $row->actual);
            $utilization = $row->budget > 0 ? (($row->committed + $row->actual) / $row->budget * 100) : 0;
            fputcsv($output, [
                $row->cc_no,
                $row->cc_name,
                $row->budget,
                $row->committed ?? 0,
                $row->actual ?? 0,
                $variance,
                number_format($utilization, 2) . '%'
            ]);
        }
        
        fclose($output);
        $csvContent = ob_get_clean();
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
