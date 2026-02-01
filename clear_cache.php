<?php
/**
 * Clear all Laravel caches without using artisan
 */

echo "Clearing caches...\n\n";

$cacheDirs = [
    'bootstrap/cache/*.php',
    'storage/framework/cache/*',
    'storage/framework/views/*.php',
    'storage/framework/sessions/*',
    'storage/logs/*.log',
];

$cleared = 0;
foreach ($cacheDirs as $pattern) {
    $files = glob($pattern);
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $cleared++;
        }
    }
}

echo "✓ Cleared $cleared cached files\n";
echo "\nAll caches cleared successfully!\n";
