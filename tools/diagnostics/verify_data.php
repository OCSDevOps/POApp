<?php
ini_set('memory_limit', '512M');
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->instance('request', Illuminate\Http\Request::capture());
$app->bootstrapWith([
    Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    Illuminate\Foundation\Bootstrap\HandleExceptions::class,
    Illuminate\Foundation\Bootstrap\RegisterFacades::class,
    Illuminate\Foundation\Bootstrap\RegisterProviders::class,
    Illuminate\Foundation\Bootstrap\BootProviders::class,
]);

use Illuminate\Support\Facades\DB;

$tables = [
    'companies', 'users', 'project_master', 'supplier_master',
    'item_master', 'purchase_order_master', 'purchase_order_details',
    'receive_order_master', 'budgets', 'supplier_users',
    'item_category_tab', 'unit_of_measure_tab', 'cost_code_master',
    'taxgroup_master', 'supplier_catalog_tab'
];

echo "=== DATA COUNTS ===\n";
foreach ($tables as $t) {
    try {
        $count = DB::table($t)->count();
        echo "  $t: $count\n";
    } catch (\Exception $e) {
        echo "  $t: ERROR - " . $e->getMessage() . "\n";
    }
}

echo "\n=== ADMIN USERS ===\n";
$users = DB::table('users')->select('name','email','username','u_type','u_status','company_id')->get();
foreach ($users as $u) {
    $role = match($u->u_type) {
        1 => 'Super Admin',
        2 => 'Company Admin',
        3 => 'Project Manager',
        4 => 'Viewer',
        0 => 'Regular User',
        default => 'Unknown'
    };
    echo "  [{$u->u_type}] {$role}: {$u->email} (username: {$u->username}, status={$u->u_status}, company={$u->company_id})\n";
}

echo "\n=== SUPPLIER USERS ===\n";
$su = DB::table('supplier_users')->select('name','email','supplier_id')->get();
foreach ($su as $s) {
    echo "  {$s->name}: {$s->email} (supplier_id={$s->supplier_id})\n";
}

echo "\n=== APP CONFIG ===\n";
echo "  APP_URL: " . config('app.url') . "\n";
echo "  DB: " . config('database.default') . " -> " . config('database.connections.' . config('database.default') . '.database') . "\n";

echo "\n=== PASSWORD VERIFY ===\n";
$admin = DB::table('users')->where('email', 'admin@demo.com')->first();
if ($admin) {
    $ok = password_verify('admin123', $admin->password);
    echo "  admin@demo.com password 'admin123': " . ($ok ? 'VALID' : 'INVALID') . "\n";
} else {
    echo "  admin@demo.com not found!\n";
}
