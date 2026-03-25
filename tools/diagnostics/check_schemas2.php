<?php
$dsn = "sqlsrv:Server=DESKTOP-Q2001NS\\SQLEXPRESS;Database=porder_db";
$pdo = new PDO($dsn, '', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$tables = ['procore_cost_code_mapping', 'procore_project_mapping', 'procore_sync_log'];

foreach ($tables as $table) {
    $stmt = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? ORDER BY ORDINAL_POSITION");
    $stmt->execute([$table]);
    $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (empty($cols)) {
        echo "$table: TABLE NOT FOUND\n\n";
    } else {
        echo "$table:\n  " . implode(', ', $cols) . "\n\n";
    }
}
