<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ReportExportService
{
    /**
     * Generate Excel-compatible CSV with formatting
     */
    public function generateExcelCsv(array $headers, Collection $data, string $filename, array $summary = null): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $response = response()->stream(function() use ($headers, $data, $summary) {
            $output = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Summary section if provided
            if ($summary) {
                foreach ($summary as $key => $value) {
                    fputcsv($output, [$key, $value]);
                }
                fputcsv($output, []); // Empty row
            }
            
            // Headers
            fputcsv($output, $headers);
            
            // Data
            foreach ($data as $row) {
                fputcsv($output, (array) $row);
            }
            
            fclose($output);
        }, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
        
        return $response;
    }
    
    /**
     * Generate multi-sheet Excel report (CSV fallback)
     * When PhpSpreadsheet is available, this can be upgraded to generate true .xlsx files
     */
    public function generateMultiSheetExcel(array $sheets, string $filename): \Illuminate\Http\Response
    {
        // For now, combine all sheets into one CSV with section headers
        ob_start();
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        foreach ($sheets as $sheetName => $sheetData) {
            // Sheet header
            fputcsv($output, []);
            fputcsv($output, ['=== ' . strtoupper($sheetName) . ' ===']);
            fputcsv($output, []);
            
            // Headers
            if (!empty($sheetData['headers'])) {
                fputcsv($output, $sheetData['headers']);
            }
            
            // Data
            if (!empty($sheetData['data'])) {
                foreach ($sheetData['data'] as $row) {
                    fputcsv($output, (array) $row);
                }
            }
        }
        
        fclose($output);
        $csvContent = ob_get_clean();
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    
    /**
     * Format currency for export
     */
    public function formatCurrency(float $amount, string $symbol = '$'): string
    {
        return $symbol . number_format($amount, 2);
    }
    
    /**
     * Format percentage for export
     */
    public function formatPercentage(float $value, int $decimals = 2): string
    {
        return number_format($value, $decimals) . '%';
    }
    
    /**
     * Generate PDF from view (using browser print as fallback)
     * When DomPDF is available, this can be upgraded to generate true PDFs
     */
    public function generatePdfFromView(string $view, array $data, string $filename): \Illuminate\View\View
    {
        // Return a print-friendly view that users can print to PDF
        return view($view, $data)->with('pdfMode', true);
    }
    
    /**
     * Get export formats configuration
     */
    public function getAvailableFormats(): array
    {
        $formats = [
            'csv' => [
                'label' => 'CSV (Excel Compatible)',
                'icon' => 'fa-file-csv',
                'class' => 'btn-success',
                'mime' => 'text/csv',
                'extension' => 'csv'
            ],
            'excel' => [
                'label' => 'Excel (.xlsx)',
                'icon' => 'fa-file-excel',
                'class' => 'btn-success',
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'extension' => 'xlsx',
                'note' => 'CSV format (install PhpSpreadsheet for true Excel)'
            ],
            'pdf' => [
                'label' => 'PDF',
                'icon' => 'fa-file-pdf',
                'class' => 'btn-danger',
                'mime' => 'application/pdf',
                'extension' => 'pdf',
                'note' => 'Print-friendly view (install DomPDF for true PDF)'
            ]
        ];
        
        return $formats;
    }
    
    /**
     * Format report data for export with proper type casting
     */
    public function formatForExport($row, array $columns): array
    {
        $formatted = [];
        
        foreach ($columns as $column => $type) {
            $value = $row->$column ?? null;
            
            switch ($type) {
                case 'currency':
                    $formatted[$column] = $this->formatCurrency((float) $value);
                    break;
                case 'percentage':
                    $formatted[$column] = $this->formatPercentage((float) $value);
                    break;
                case 'date':
                    $formatted[$column] = $value ? date('Y-m-d', strtotime($value)) : '';
                    break;
                case 'datetime':
                    $formatted[$column] = $value ? date('Y-m-d H:i:s', strtotime($value)) : '';
                    break;
                case 'status':
                    $formatted[$column] = ucwords(str_replace('_', ' ', $value));
                    break;
                default:
                    $formatted[$column] = $value;
            }
        }
        
        return $formatted;
    }
    
    /**
     * Generate budget report summary for export
     */
    public function generateBudgetSummary(array $summary): array
    {
        if (!$summary) {
            return [];
        }
        
        return [
            'REPORT SUMMARY' => '',
            'Total Original Budget' => $this->formatCurrency($summary['total_original']),
            'Total Revised Budget' => $this->formatCurrency($summary['total_revised']),
            'Total Committed' => $this->formatCurrency($summary['total_committed']),
            'Total Actual' => $this->formatCurrency($summary['total_actual']),
            'Total Spent' => $this->formatCurrency($summary['total_spent']),
            'Total Remaining' => $this->formatCurrency($summary['total_remaining']),
            'Overall Utilization' => $this->formatPercentage($summary['overall_utilization']),
            'On Track Count' => $summary['on_track_count'],
            'At Risk Count' => $summary['at_risk_count'],
            'Over Budget Count' => $summary['over_budget_count'],
        ];
    }
    
    /**
     * Generate change order summary for export
     */
    public function generateChangeOrderSummary(array $summary): array
    {
        if (!$summary) {
            return [];
        }
        
        return [
            'REPORT SUMMARY' => '',
            'Total Change Orders' => $summary['total_count'],
            'Total Increases' => $this->formatCurrency($summary['total_increase']),
            'Total Decreases' => $this->formatCurrency($summary['total_decrease']),
            'Net Change' => ($summary['net_change'] >= 0 ? '+' : '-') . $this->formatCurrency(abs($summary['net_change'])),
            'Approved' => $summary['approved_count'],
            'Pending' => $summary['pending_count'],
            'Rejected' => $summary['rejected_count'],
            'Draft' => $summary['draft_count'],
            'Budget COs' => $summary['budget_co_count'],
            'PO COs' => $summary['po_co_count'],
        ];
    }
    
    /**
     * Generate committed vs actual summary for export
     */
    public function generateCommittedActualSummary(array $summary): array
    {
        if (!$summary) {
            return [];
        }
        
        return [
            'REPORT SUMMARY' => '',
            'Total Budget' => $this->formatCurrency($summary['total_budget']),
            'Total Committed' => $this->formatCurrency($summary['cumulative_committed']),
            'Total Actual' => $this->formatCurrency($summary['cumulative_actual']),
            'Remaining Budget' => $this->formatCurrency($summary['remaining_budget']),
            'Utilization Rate' => $this->formatPercentage($summary['utilization_rate']),
            'Period Committed' => $this->formatCurrency($summary['period_committed']),
            'Period Actual' => $this->formatCurrency($summary['period_actual']),
            'Period Variance' => $this->formatCurrency($summary['variance']),
            'PO Count' => $summary['po_count'],
            'RO Count' => $summary['ro_count'],
        ];
    }
}
