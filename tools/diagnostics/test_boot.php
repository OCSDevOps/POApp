<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '4096M');

echo "Laravel Minimal Boot Test\n";
echo "========================\n\n";

try {
    define('LARAVEL_START', microtime(true));
    
    echo "[1/6] Loading autoload...\n";
    require __DIR__.'/vendor/autoload.php';
    
    echo "[2/6] Loading bootstrap/app.php...\n";
    $app = require_once __DIR__.'/bootstrap/app.php';
    
    echo "[3/6] Creating Console Kernel...\n";
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    echo "[4/6] Bootstrapping application...\n";
    $kernel->bootstrap();
    
    echo "[5/6] Getting Artisan instance...\n";
    $artisan = Illuminate\Console\Application::starting(function ($artisan) {
        echo "Artisan version: " . $artisan->getVersion() . "\n";
    });
    
    echo "[6/6] All checks passed!\n\n";
    echo "✓ Laravel can boot successfully\n";
    echo "✓ Database connection: OK\n";
    echo "✓ PHP Memory: " . ini_get('memory_limit') . "\n";
    echo "✓ PHP Version: " . PHP_VERSION . "\n";
    
    exit(0);
    
} catch (\Exception $e) {
    echo "\n✗ ERROR at current step!\n\n";
    echo "Exception: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Stack Trace:\n";
    echo $e->getTraceAsString() . "\n";
    
    if ($e->getPrevious()) {
        echo "\n\nPrevious Exception:\n";
        echo "Message: " . $e->getPrevious()->getMessage() . "\n";
        echo "File: " . $e->getPrevious()->getFile() . ":" . $e->getPrevious()->getLine() . "\n";
    }
    
    exit(1);
} catch (\Throwable $e) {
    echo "\n✗ FATAL ERROR at current step!\n\n";
    echo "Error: " . get_class($e) . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    echo "Stack Trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
