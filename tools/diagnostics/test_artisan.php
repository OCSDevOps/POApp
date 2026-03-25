<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "Starting test...\n";

try {
    define('LARAVEL_START', microtime(true));
    
    echo "Loading autoload...\n";
    require __DIR__.'/vendor/autoload.php';
    
    echo "Loading bootstrap...\n";
    $app = require_once __DIR__.'/bootstrap/app.php';
    
    echo "Creating kernel...\n";
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    
    echo "Running command...\n";
    $status = $kernel->handle(
        $input = new Symfony\Component\Console\Input\ArgvInput,
        new Symfony\Component\Console\Output\ConsoleOutput
    );
    
    echo "Terminating...\n";
    $kernel->terminate($input, $status);
    
    exit($status);
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
} catch (\Throwable $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
