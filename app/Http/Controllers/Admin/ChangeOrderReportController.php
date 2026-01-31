<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ChangeOrderReportController extends Controller
{
    /**
     * Display change orders report with filtering
     */
    public function index(Request $request)
    {
        $companyId = Session::get('company_id', 1);
        
        // Get filter parameters
        $filters = [
            'project_id' => $request->get('project_id'),
            'co_type' => $request->get('co_type', 'all'), // all, budget, po
            'status' => $request->get('status', 'all'),
            'date_from' => $request->get('date_from', now()->subMonths(3)->format('Y-m-d')),
            'date_to' => $request->get('date_to', now()->format('Y-m-d')),
        ];
        
        // Get projects for filter dropdown
        $projects = DB::table('project_master')
            ->where('proj_company_id', $companyId)
            ->where('proj_status', 1)
            ->select('proj_id', 'proj_name', 'proj_no')
            ->orderBy('proj_name')
            ->get();
        
        $budgetChangeOrders = collect();
        $poChangeOrders = collect();
        $summary = null;
        
        // Fetch Budget Change Orders
        if (in_array($filters['co_type'], ['all', 'budget'])) {
            $budgetChangeOrders = $this->getBudgetChangeOrders($companyId, $filters);
        }
        
        // Fetch PO Change Orders
        if (in_array($filters['co_type'], ['all', 'po'])) {
            $poChangeOrders = $this->getPoChangeOrders($companyId, $filters);
        }
        
        // Calculate summary
        if ($filters['co_type'] !== 'all' || $budgetChangeOrders->isNotEmpty() || $poChangeOrders->isNotEmpty()) {
            $summary = $this->calculateSummary($budgetChangeOrders, $poChangeOrders);
        }
        
        return view('admin.reports.change-orders', compact(
            'projects',
            'filters',
            'budgetChangeOrders',
            'poChangeOrders',
            'summary'
        ));
    }
    
    /**
     * Get Budget Change Orders with filters
     */
    private function getBudgetChangeOrders($companyId, $filters)
    {
        $query = DB::table('budget_change_orders as bco')
            ->join('project_master as p', 'bco.project_id', '=', 'p.proj_id')
            ->join('cost_code_master as cc', 'bco.cost_code_id', '=', 'cc.cc_id')
            ->leftJoin('user_master as creator', 'bco.created_by', '=', 'creator.u_id')
            ->leftJoin('user_master as approver', 'bco.approved_by', '=', 'approver.u_id')
            ->where('bco.company_id', $companyId)
            ->whereBetween('bco.created_at', [$filters['date_from'] . ' 00:00:00', $filters['date_to'] . ' 23:59:59'])
            ->select(
                DB::raw("'budget' as co_type"),
                'bco.bco_id as id',
                'bco.bco_number as number',
                'p.proj_name as project_name',
                'p.proj_no as project_number',
                'cc.cc_no as cost_code',
                'cc.cc_name as cost_code_name',
                'bco.bco_type as type',
                'bco.bco_amount as amount',
                'bco.previous_budget',
                'bco.new_budget',
                'bco.bco_status as status',
                'bco.bco_reason as reason',
                'bco.bco_reference as reference',
                DB::raw("CONCAT(creator.u_fname, ' ', creator.u_lname) as created_by_name"),
                DB::raw("CONCAT(approver.u_fname, ' ', approver.u_lname) as approved_by_name"),
                'bco.approved_at',
                'bco.created_at',
                'bco.rejection_reason'
            );
        
        if ($filters['project_id']) {
            $query->where('bco.project_id', $filters['project_id']);
        }
        
        if ($filters['status'] !== 'all') {
            $query->where('bco.bco_status', $filters['status']);
        }
        
        return $query->orderBy('bco.created_at', 'desc')->get();
    }
    
    /**
     * Get PO Change Orders with filters
     */
    private function getPoChangeOrders($companyId, $filters)
    {
        $query = DB::table('po_change_orders as poco')
            ->join('purchase_order_master as pom', 'poco.purchase_order_id', '=', 'pom.porder_id')
            ->join('project_master as p', 'pom.porder_project_ms', '=', 'p.proj_id')
            ->leftJoin('user_master as creator', 'poco.created_by', '=', 'creator.u_id')
            ->leftJoin('user_master as approver', 'poco.approved_by', '=', 'approver.u_id')
            ->where('poco.company_id', $companyId)
            ->whereBetween('poco.created_at', [$filters['date_from'] . ' 00:00:00', $filters['date_to'] . ' 23:59:59'])
            ->select(
                DB::raw("'po' as co_type"),
                'poco.poco_id as id',
                'poco.poco_number as number',
                'p.proj_name as project_name',
                'p.proj_no as project_number',
                DB::raw('NULL as cost_code'),
                DB::raw('NULL as cost_code_name'),
                'poco.poco_type as type',
                'poco.poco_amount as amount',
                'poco.previous_total as previous_budget',
                'poco.new_total as new_budget',
                'poco.poco_status as status',
                'poco.poco_description as reason',
                'poco.poco_reference as reference',
                DB::raw("CONCAT(creator.u_fname, ' ', creator.u_lname) as created_by_name"),
                DB::raw("CONCAT(approver.u_fname, ' ', approver.u_lname) as approved_by_name"),
                'poco.approved_at',
                'poco.created_at',
                'poco.rejection_reason'
            );
        
        if ($filters['project_id']) {
            $query->where('p.proj_id', $filters['project_id']);
        }
        
        if ($filters['status'] !== 'all') {
            $query->where('poco.poco_status', $filters['status']);
        }
        
        return $query->orderBy('poco.created_at', 'desc')->get();
    }
    
    /**
     * Calculate summary statistics
     */
    private function calculateSummary($budgetChangeOrders, $poChangeOrders)
    {
        $allOrders = $budgetChangeOrders->merge($poChangeOrders);
        
        $totalCount = $allOrders->count();
        $totalIncrease = $allOrders->where('amount', '>', 0)->sum('amount');
        $totalDecrease = $allOrders->where('amount', '<', 0)->sum('amount');
        $netChange = $totalIncrease + $totalDecrease;
        
        return [
            'total_count' => $totalCount,
            'total_increase' => $totalIncrease,
            'total_decrease' => abs($totalDecrease),
            'net_change' => $netChange,
            'approved_count' => $allOrders->where('status', 'approved')->count(),
            'pending_count' => $allOrders->where('status', 'pending_approval')->count(),
            'rejected_count' => $allOrders->where('status', 'rejected')->count(),
            'draft_count' => $allOrders->where('status', 'draft')->count(),
            'budget_co_count' => $budgetChangeOrders->count(),
            'po_co_count' => $poChangeOrders->count(),
        ];
    }
    
    /**
     * Export change orders to Excel or CSV
     */
    public function export(Request $request)
    {
        $companyId = Session::get('company_id', 1);
        $format = $request->get('format', 'csv'); // csv or excel
        
        $filters = [
            'project_id' => $request->get('project_id'),
            'co_type' => $request->get('co_type', 'all'),
            'status' => $request->get('status', 'all'),
            'date_from' => $request->get('date_from', now()->subMonths(3)->format('Y-m-d')),
            'date_to' => $request->get('date_to', now()->format('Y-m-d')),
        ];
        
        $budgetChangeOrders = collect();
        $poChangeOrders = collect();
        
        if (in_array($filters['co_type'], ['all', 'budget'])) {
            $budgetChangeOrders = $this->getBudgetChangeOrders($companyId, $filters);
        }
        
        if (in_array($filters['co_type'], ['all', 'po'])) {
            $poChangeOrders = $this->getPoChangeOrders($companyId, $filters);
        }
        
        $allOrders = $budgetChangeOrders->merge($poChangeOrders)->sortByDesc('created_at');
        
        // Generate CSV content
        $headers = [
            'Type',
            'CO Number',
            'Project',
            'Cost Code',
            'Change Type',
            'Amount',
            'Previous Budget',
            'New Budget',
            'Status',
            'Reason',
            'Reference',
            'Created By',
            'Approved By',
            'Approved Date',
            'Created Date'
        ];
        
        $filename = 'change_orders_report_' . now()->format('Y-m-d_His') . '.csv';
        
        // Use output buffering to generate CSV
        ob_start();
        $output = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers
        fputcsv($output, $headers);
        
        // Data rows
        foreach ($allOrders as $order) {
            fputcsv($output, [
                $order->co_type === 'budget' ? 'Budget CO' : 'PO CO',
                $order->number,
                $order->project_name . ' (' . $order->project_number . ')',
                $order->cost_code ? $order->cost_code . ' - ' . $order->cost_code_name : 'N/A',
                ucwords(str_replace('_', ' ', $order->type)),
                $order->amount,
                $order->previous_budget,
                $order->new_budget,
                ucwords(str_replace('_', ' ', $order->status)),
                $order->reason,
                $order->reference ?? 'N/A',
                $order->created_by_name ?? 'N/A',
                $order->approved_by_name ?? 'N/A',
                $order->approved_at ? date('Y-m-d', strtotime($order->approved_at)) : 'N/A',
                date('Y-m-d', strtotime($order->created_at))
            ]);
        }
        
        fclose($output);
        $csvContent = ob_get_clean();
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
