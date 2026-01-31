<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "Checking Phase 1.3-1.5 Tables:\n";
echo "================================\n\n";

$tables = [
    'item_pricing' => 'Phase 1.3: Item Pricing',
    'rfqs' => 'Phase 1.4: RFQs',
    'rfq_items' => 'Phase 1.4: RFQ Items',
    'rfq_suppliers' => 'Phase 1.4: RFQ Suppliers',
    'rfq_quotes' => 'Phase 1.4: RFQ Quotes',
];

foreach ($tables as $table => $description) {
    try {
        $count = DB::table($table)->count();
        echo "✓ $table table EXISTS ($description) - Records: $count\n";
    } catch (\Exception $e) {
        echo "✗ $table table NOT FOUND ($description)\n";
    }
}

// Check backorder fields
echo "\nChecking Phase 1.5: Backorder Fields:\n";
echo "======================================\n";
try {
    $columns = DB::select("SHOW COLUMNS FROM purchase_order_items WHERE Field LIKE '%backorder%'");
    if (count($columns) > 0) {
        echo "✓ Backorder fields exist in purchase_order_items:\n";
        foreach ($columns as $col) {
            echo "  - {$col->Field} ({$col->Type})\n";
        }
    } else {
        echo "✗ No backorder fields found\n";
    }
} catch (\Exception $e) {
    echo "✗ Error checking backorder fields: " . $e->getMessage() . "\n";
}
