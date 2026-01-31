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
        $companyId = Session::get('company_id', 1);
        
        // Get monthly committed (PO) data
        $committedData = DB::table('purchase_order_master as pom')
            ->join('purchase_order_details as pod', 'pom.porder_id', '=', 'pod.po_detail_porder_ms')
            ->where('pom.porder_project_ms', $projectId)
            ->where('pom.company_id', $companyId)
            ->where('pom.porder_general_status', '>=', 'submitted') // Approved POs only
            ->whereBetween('pom.porder_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select(
                DB::raw("FORMAT(pom.porder_date, 'yyyy-MM') as month"),
                DB::raw('SUM(pod.po_detail_unitprice * pod.po_detail_quantity) as amount')
            )
            ->groupBy(DB::raw("FORMAT(pom.porder_date, 'yyyy-MM')"))
            ->orderBy(DB::raw("FORMAT(pom.porder_date, 'yyyy-MM')"))
            ->get()
            ->keyBy('month');
        
        // Get monthly actual (Receive Order) data
        $actualData = DB::table('receive_order_master as rom')
            ->join('receive_order_details as rod', 'rom.rorder_id', '=', 'rod.ro_detail_rorder_ms')
            ->where('rom.rorder_project_ms', $projectId)
            ->where('rom.company_id', $companyId)
            ->where('rom.rorder_status', '>=', 1) // Completed ROs only
            ->whereBetween('rom.rorder_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select(
                DB::raw("FORMAT(rom.rorder_date, 'yyyy-MM') as month"),
                DB::raw('SUM(rod.ro_detail_quantity * rod.ro_detail_unitprice) as amount')
            )
            ->groupBy(DB::raw("FORMAT(rom.rorder_date, 'yyyy-MM')"))
            ->orderBy(DB::raw("FORMAT(rom.rorder_date, 'yyyy-MM')"))
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
        $companyId = Session::get('company_id', 1);
        
        // Total budget for project (company-scoped)
        $totalBudget = DB::table('budget_master')
            ->where('budget_project_id', $projectId)
            ->where('company_id', $companyId)
            ->sum('budget_revised_amount');
        
        // Total committed in period (company-scoped)
        $totalCommitted = DB::table('purchase_order_master as pom')
            ->join('purchase_order_details as pod', 'pom.porder_id', '=', 'pod.po_detail_porder_ms')
            ->where('pom.porder_project_ms', $projectId)
            ->where('pom.company_id', $companyId)
            ->where('pom.porder_general_status', '>=', 'submitted')
            ->whereBetween('pom.porder_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum(DB::raw('pod.po_detail_unitprice * pod.po_detail_quantity'));
        
        // Total actual in period (company-scoped)
        $totalActual = DB::table('receive_order_master as rom')
            ->join('receive_order_details as rod', 'rom.rorder_id', '=', 'rod.ro_detail_rorder_ms')
            ->where('rom.rorder_project_ms', $projectId)
            ->where('rom.company_id', $companyId)
            ->where('rom.rorder_status', '>=', 1)
            ->whereBetween('rom.rorder_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum(DB::raw('rod.ro_detail_quantity * rod.ro_detail_unitprice'));
        
        // Cumulative totals (all time, company-scoped)
        $cumulativeCommitted = DB::table('purchase_order_master as pom')
            ->join('purchase_order_details as pod', 'pom.porder_id', '=', 'pod.po_detail_porder_ms')
            ->where('pom.porder_project_ms', $projectId)
            ->where('pom.company_id', $companyId)
            ->where('pom.porder_general_status', '>=', 'submitted')
            ->sum(DB::raw('pod.po_detail_unitprice * pod.po_detail_quantity'));
        
        $cumulativeActual = DB::table('receive_order_master as rom')
            ->join('receive_order_details as rod', 'rom.rorder_id', '=', 'rod.ro_detail_rorder_ms')
            ->where('rom.rorder_project_ms', $projectId)
            ->where('rom.company_id', $companyId)
            ->where('rom.rorder_status', '>=', 1)
            ->sum(DB::raw('rod.ro_detail_quantity * rod.ro_detail_unitprice'));
        
        // PO Count (company-scoped)
        $poCount = DB::table('purchase_order_master')
            ->where('porder_project_ms', $projectId)
            ->where('company_id', $companyId)
            ->where('porder_general_status', '>=', 'submitted')
            ->whereBetween('porder_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->count();
        
        // RO Count (company-scoped)
        $roCount = DB::table('receive_order_master')
            ->where('rorder_project_ms', $projectId)
            ->where('company_id', $companyId)
            ->where('rorder_status', '>=', 1)
            ->whereBetween('rorder_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
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
        $companyId = Session::get('company_id', 1);
        
        return DB::table('budget_master as b')
            ->join('cost_code_master as cc', 'b.budget_cost_code_id', '=', 'cc.cc_id')
            ->where('b.budget_project_id', $projectId)
            ->where('b.company_id', $companyId)
            ->where('cc.company_id', $companyId)
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
        
        // Get project info (company-scoped)
        $companyId = Session::get('company_id', 1);
        $project = DB::table('project_master')
            ->where('proj_id', $projectId)
            ->where('proj_company_id', $companyId)
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
