<?php
/**
 * Create the storage:link symlink without artisan (avoids OOM).
 * On Windows without admin, falls back to a junction.
 */
$link = __DIR__ . '/public/storage';
$target = __DIR__ . '/storage/app/public';

if (file_exists($link) || is_link($link)) {
    echo "Storage link already exists at: $link\n";
    exit(0);
}

if (!is_dir($target)) {
    mkdir($target, 0755, true);
    echo "Created target directory: $target\n";
}

// Try symlink first
if (@symlink($target, $link)) {
    echo "Symlink created: $link -> $target\n";
    exit(0);
}

// Fallback: try junction (Windows, no admin needed)
$cmd = 'mklink /J "' . str_replace('/', '\\', $link) . '" "' . str_replace('/', '\\', $target) . '"';
exec($cmd, $output, $retval);
if ($retval === 0) {
    echo "Junction created: $link -> $target\n";
} else {
    echo "Failed to create link. Run as administrator or manually:\n";
    echo "  mklink /D \"" . str_replace('/', '\\', $link) . "\" \"" . str_replace('/', '\\', $target) . "\"\n";
}
