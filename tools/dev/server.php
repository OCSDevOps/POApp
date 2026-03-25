<?php

/**
 * Laravel development server router
 * This ensures static assets are served properly
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

// Otherwise, pass to Laravel
require_once __DIR__ . '/public/index.php';
