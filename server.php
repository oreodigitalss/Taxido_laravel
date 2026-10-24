<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @author Taylor Otwell <taylor@laravel.com>
 */
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/') {
    $publicFile = __DIR__ . '/public' . $uri;

    if (is_file($publicFile)) {
        // When this router is used with the project root as the built-in
        // server document root, let PHP serve public assets from Laravel's
        // actual public directory instead of looking for /front, /css, etc.
        if (strtolower(pathinfo($publicFile, PATHINFO_EXTENSION)) !== 'php') {
            $extension = strtolower(pathinfo($publicFile, PATHINFO_EXTENSION));
            $mimeTypes = [
                'css' => 'text/css; charset=UTF-8',
                'js' => 'application/javascript; charset=UTF-8',
                'json' => 'application/json; charset=UTF-8',
                'svg' => 'image/svg+xml',
                'woff' => 'font/woff',
                'woff2' => 'font/woff2',
                'ttf' => 'font/ttf',
                'eot' => 'application/vnd.ms-fontobject',
            ];
            $mimeType = $mimeTypes[$extension] ?? mime_content_type($publicFile) ?: 'application/octet-stream';
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . filesize($publicFile));
            readfile($publicFile);

            return true;
        }

        return false;
    }
}

require_once __DIR__ . '/public/index.php';
