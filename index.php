<?php
/**
 * Entry point for LTV Office
 * Routes requests to either frontend (dist/) or backend API
 * 
 * NOTE: This file should only be called for SPA routes.
 * Static files (CSS, JS, images) should be served directly by .htaccess
 */

$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Remove query string for path checking
$path = strtok($requestPath, '?');

// If request is for backend API
if (strpos($path, '/backend/') === 0 || strpos($path, '/api/') === 0) {
    // Let backend handle it
    $backendPath = __DIR__ . $path;
    if (file_exists($backendPath) && is_file($backendPath)) {
        require_once $backendPath;
        exit;
    }
}

// Check if it's a static file request (shouldn't reach here if .htaccess works)
$extensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'woff', 'woff2', 'ttf', 'eot'];
$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
if (in_array($extension, $extensions)) {
    // Try to serve from dist/
    $staticFile = __DIR__ . '/dist' . $path;
    if (file_exists($staticFile)) {
        // Set correct MIME type
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
        ];
        if (isset($mimeTypes[$extension])) {
            header('Content-Type: ' . $mimeTypes[$extension]);
        }
        readfile($staticFile);
        exit;
    }
}

// Otherwise, serve frontend SPA from dist/
$distIndex = __DIR__ . '/dist/index.html';
if (file_exists($distIndex)) {
    header('Content-Type: text/html; charset=utf-8');
    readfile($distIndex);
} else {
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo 'Frontend not found. Please run "npm run build" first.';
}

