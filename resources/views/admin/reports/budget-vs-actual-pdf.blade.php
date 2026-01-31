<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Budget Report - {{ $project->proj_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            font-size: 18pt;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 10pt;
            color: #666;
        }
        .summary-section {
            margin-bottom: 20px;
        }
        .summary-section h2 {
            font-size: 12pt;
            margin-bottom: 10px;
            background: #f0f0f0;
            padding: 5px;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .summary-row {
            display: table-row;
        }
        .summary-cell {
            display: table-cell;
            padding: 8px;
            border: 1px solid #ddd;
            width: 33.33%;
        }
        .summary-cell strong {
            display: block;
            font-size: 14pt;
            color: #333;
        }
        .summary-cell span {
            font-size: 9pt;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 9pt;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status-success {
            color: #28a745;
        }
        .status-warning {
            color: #ffc107;
        }
        .status-danger {
            color: #dc3545;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 8pt;
            color: #666;
            text-align: center;
        }
        .level-1 {
            font-weight: bold;
            background: #f8f9fa;
        }
        .level-2 {
            padding-left: 15px;
        }
        .level-3 {
            padding-left: 30px;
            font-size: 8.5pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Budget vs Actual Report</h1>
        <p><strong>{{ $project->proj_name }}</strong> ({{ $project->proj_no }})</p>
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>

    @if($summary)
    <div class="summary-section">
        <h2>Executive Summary</h2>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <strong>${{ number_format($summary['total_revised'], 0) }}</strong>
                    <span>Total Budget</span>
                </div>
                <div class="summary-cell">
                    <strong>${{ number_format($summary['total_committed'], 0) }}</strong>
                    <span>Committed (POs)</span>
                </div>
                <div class="summary-cell">
                    <strong>${{ number_format($summary['total_actual'], 0) }}</strong>
                    <span>Actual Spend</span>
                </div>
            </div>
            <div class="summary-row">
                <div class="summary-cell">
                    <strong>${{ number_format($summary['total_spent'], 0) }}</strong>
                    <span>Total Spent</span>
                </div>
                <div class="summary-cell">
                    <strong>${{ number_format($summary['total_remaining'], 0) }}</strong>
                    <span>Remaining</span>
                </div>
                <div class="summary-cell">
                    <strong>{{ number_format($summary['overall_utilization'], 1) }}%</strong>
                    <span>Overall Utilization</span>
                </div>
            </div>
        </div>

        <h3>Budget Status Summary</h3>
        <table style="width: 60%; margin-bottom: 15px;">
            <tr>
                <td class="status-success"><strong>On Track (&lt;75%)</strong></td>
                <td class="text-right">{{ $summary['on_track_count'] }} Cost Codes</td>
            </tr>
            <tr>
                <td class="status-warning"><strong>At Risk (75-99%)</strong></td>
                <td class="text-right">{{ $summary['at_risk_count'] }} Cost Codes</td>
            </tr>
            <tr>
                <td class="status-danger"><strong>Over Budget (≥100%)</strong></td>
                <td class="text-right">{{ $summary['over_budget_count'] }} Cost Codes</td>
            </tr>
        </table>
    </div>
    @endif

    <div class="detail-section">
        <h2>Detailed Breakdown by Cost Code</h2>
        <table>
            <thead>
                <tr>
                    <th>Cost Code</th>
                    <th>Description</th>
                    <th class="text-right">Original</th>
                    <th class="text-right">Revised</th>
                    <th class="text-right">Committed</th>
                    <th class="text-right">Actual</th>
                    <th class="text-right">Variance</th>
                    <th class="text-center">Util %</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $row)
                @php
                    $status = 'On Track';
                    $statusClass = 'status-success';
                    if ($row->utilization_pct >= 100) {
                        $status = 'Over Budget';
                        $statusClass = 'status-danger';
                    } elseif ($row->utilization_pct >= 90) {
                        $status = 'Critical';
                        $statusClass = 'status-danger';
                    } elseif ($row->utilization_pct >= 75) {
                        $status = 'At Risk';
                        $statusClass = 'status-warning';
                    }
                @endphp
                <tr class="level-{{ $row->level }}">
                    <td>{{ $row->cost_code }}</td>
                    <td>{{ $row->cost_code_name }}</td>
                    <td class="text-right">${{ number_format($row->original, 2) }}</td>
                    <td class="text-right">${{ number_format($row->revised, 2) }}</td>
                    <td class="text-right">${{ number_format($row->committed, 2) }}</td>
                    <td class="text-right">${{ number_format($row->actual, 2) }}</td>
                    <td class="text-right {{ $row->variance < 0 ? 'status-danger' : 'status-success' }}">
                        ${{ number_format($row->variance, 2) }}
                    </td>
                    <td class="text-center">{{ number_format($row->utilization_pct, 1) }}%</td>
                    <td class="{{ $statusClass }}">{{ $status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Confidential - For internal use only</p>
        <p>Page 1 of 1 | {{ $reportData->count() }} cost codes reported</p>
    </div>
</body>
</html>
