<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order - {{ $purchaseOrder->porder_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
            padding: 30px;
        }

        /* Header */
        .po-header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #333;
        }
        .po-header-left {
            display: table-cell;
            vertical-align: top;
            width: 60%;
        }
        .po-header-right {
            display: table-cell;
            vertical-align: top;
            width: 40%;
            text-align: right;
        }
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            color: #222;
            margin-bottom: 5px;
        }
        .company-details {
            font-size: 9pt;
            color: #666;
        }
        .po-title {
            font-size: 16pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .po-number {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .po-meta {
            font-size: 9pt;
            color: #555;
        }

        /* Info Blocks */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-block {
            display: table-cell;
            vertical-align: top;
            width: 50%;
            padding-right: 15px;
        }
        .info-block:last-child {
            padding-right: 0;
            padding-left: 15px;
        }
        .info-block-title {
            font-size: 10pt;
            font-weight: bold;
            color: #333;
            border-bottom: 1px solid #999;
            padding-bottom: 4px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-block p {
            margin-bottom: 3px;
            font-size: 9.5pt;
        }
        .info-block p strong {
            display: inline-block;
            min-width: 100px;
        }

        /* Items Table */
        .items-section {
            margin-bottom: 20px;
        }
        .items-section h3 {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        th, td {
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }

        /* Totals */
        .totals-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .totals-spacer {
            display: table-cell;
            width: 60%;
        }
        .totals-table {
            display: table-cell;
            width: 40%;
        }
        .totals-table table {
            border: none;
        }
        .totals-table td {
            border: none;
            padding: 4px 8px;
            font-size: 9.5pt;
        }
        .totals-table .grand-total td {
            border-top: 2px solid #333;
            font-size: 11pt;
            font-weight: bold;
            padding-top: 8px;
        }

        /* Notes & Terms */
        .notes-section {
            margin-bottom: 15px;
        }
        .notes-section h4 {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #555;
            margin-bottom: 4px;
        }
        .notes-section p {
            font-size: 9pt;
            color: #444;
            padding: 6px;
            background-color: #fafafa;
            border: 1px solid #ddd;
        }

        /* Footer */
        .po-footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
            font-size: 8pt;
            color: #999;
            text-align: center;
        }

        /* Print styles */
        @media print {
            body {
                padding: 15px;
            }
            .po-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
            }
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="po-header">
        <div class="po-header-left">
            <div class="company-name">
                {{ $purchaseOrder->project->company->name ?? ($purchaseOrder->project->proj_company_name ?? session('company_name', 'Company Name')) }}
            </div>
            <div class="company-details">
                @if($purchaseOrder->project->company->address ?? null)
                    {{ $purchaseOrder->project->company->address }}<br>
                @endif
                @if($purchaseOrder->project->company->phone ?? null)
                    Phone: {{ $purchaseOrder->project->company->phone }}
                @endif
            </div>
        </div>
        <div class="po-header-right">
            <div class="po-title">PURCHASE ORDER</div>
            <div class="po-number">{{ $purchaseOrder->porder_no }}</div>
            <div class="po-meta">
                <p><strong>Date:</strong> {{ $purchaseOrder->porder_createdate ? date('M d, Y', strtotime($purchaseOrder->porder_createdate)) : 'N/A' }}</p>
                @if($purchaseOrder->porder_delivery_note)
                    <p><strong>Delivery Note:</strong> {{ \Illuminate\Support\Str::limit($purchaseOrder->porder_delivery_note, 50) }}</p>
                @endif
                @if($purchaseOrder->porder_description)
                    <p><strong>Description:</strong> {{ \Illuminate\Support\Str::limit($purchaseOrder->porder_description, 50) }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Supplier & Delivery Info --}}
    <div class="info-section">
        <div class="info-block">
            <div class="info-block-title">Supplier</div>
            <p><strong>Name:</strong> {{ $purchaseOrder->supplier->sup_name ?? 'N/A' }}</p>
            @if($purchaseOrder->supplier->sup_contact ?? null)
                <p><strong>Contact:</strong> {{ $purchaseOrder->supplier->sup_contact }}</p>
            @endif
            @if($purchaseOrder->supplier->sup_email ?? null)
                <p><strong>Email:</strong> {{ $purchaseOrder->supplier->sup_email }}</p>
            @endif
            @if($purchaseOrder->supplier->sup_phone ?? null)
                <p><strong>Phone:</strong> {{ $purchaseOrder->supplier->sup_phone }}</p>
            @endif
            @if($purchaseOrder->supplier->sup_address ?? null)
                <p><strong>Address:</strong> {{ $purchaseOrder->supplier->sup_address }}</p>
            @endif
        </div>
        <div class="info-block">
            <div class="info-block-title">Deliver To</div>
            <p><strong>Project:</strong> {{ $purchaseOrder->project->proj_name ?? 'N/A' }}</p>
            @if($purchaseOrder->porder_address)
                <p><strong>Address:</strong> {{ $purchaseOrder->porder_address }}</p>
            @elseif($purchaseOrder->project->proj_address ?? null)
                <p><strong>Address:</strong> {{ $purchaseOrder->project->proj_address }}</p>
            @endif
        </div>
    </div>

    {{-- Items Table --}}
    <div class="items-section">
        <h3>Order Items</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;" class="text-center">#</th>
                    <th style="width: 14%;">Item Code</th>
                    <th style="width: 30%;">Description</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 14%;" class="text-right">Unit Price</th>
                    <th style="width: 12%;" class="text-right">Tax</th>
                    <th style="width: 12%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseOrder->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->po_detail_item }}</td>
                        <td>{{ $item->po_detail_sku }}</td>
                        <td class="text-center">{{ $item->po_detail_quantity }}</td>
                        <td class="text-right">${{ number_format($item->po_detail_unitprice, 2) }}</td>
                        <td class="text-right">${{ number_format($item->po_detail_taxamount, 2) }}</td>
                        <td class="text-right">${{ number_format($item->po_detail_total, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No items</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div class="totals-section">
        <div class="totals-spacer"></div>
        <div class="totals-table">
            <table>
                <tr>
                    <td class="text-right"><strong>Subtotal:</strong></td>
                    <td class="text-right">${{ number_format($purchaseOrder->porder_total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-right"><strong>Tax:</strong></td>
                    <td class="text-right">${{ number_format($purchaseOrder->porder_total_tax, 2) }}</td>
                </tr>
                <tr class="grand-total">
                    <td class="text-right">Grand Total:</td>
                    <td class="text-right">${{ number_format($purchaseOrder->grand_total, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Description --}}
    @if($purchaseOrder->porder_description)
        <div class="notes-section">
            <h4>Description</h4>
            <p>{{ $purchaseOrder->porder_description }}</p>
        </div>
    @endif

    {{-- Delivery Note --}}
    @if($purchaseOrder->porder_delivery_note)
        <div class="notes-section">
            <h4>Delivery Note</h4>
            <p>{{ $purchaseOrder->porder_delivery_note }}</p>
        </div>
    @endif

    {{-- Footer --}}
    <div class="po-footer">
        <p>This is a computer-generated document. | {{ $purchaseOrder->porder_no }} | Generated on {{ now()->format('F j, Y') }}</p>
    </div>
</body>
</html>
