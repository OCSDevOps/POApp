<?php
// Direct PDO connection - no Laravel bootstrap (avoids OOM)
$dsn = "sqlsrv:Server=DESKTOP-Q2001NS\\SQLEXPRESS;Database=porder_db";
$pdo = new PDO($dsn, '', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$tables = ['user_info', 'users', 'master_user_type', 'supplier_catalog_tab',
           'approval_workflows', 'project_master', 'request_purchase_order'];

foreach ($tables as $table) {
    $stmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? ORDER BY ORDINAL_POSITION");
    $stmt->execute([$table]);
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (empty($cols)) {
        echo "$table: TABLE NOT FOUND\n\n";
    } else {
        echo "$table: " . implode(', ', $cols) . "\n\n";
    }
}

// Check views
$views = ['vw_budget_summary', 'vw_receiving_summary', 'vw_back_order_report', 'vw_item_pricing_summary'];
foreach ($views as $view) {
    $stmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? ORDER BY ORDINAL_POSITION");
    $stmt->execute([$view]);
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (empty($cols)) {
        echo "$view: VIEW NOT FOUND\n\n";
    } else {
        echo "$view: " . implode(', ', $cols) . "\n\n";
    }
}

// PO columns
$stmt = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'purchase_order_master' ORDER BY ORDINAL_POSITION");
echo "purchase_order_master columns:\n  " . implode(', ', $stmt->fetchAll(PDO::FETCH_COLUMN)) . "\n";
