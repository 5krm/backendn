<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Ensure /tmp storage directory structure exists for Vercel serverless environment
$storageDirs = [
    '/tmp/storage',
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/views',
    '/tmp/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

if (! file_exists(__DIR__.'/../vendor/autoload.php')) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<h2>⚠️ Vercel Error: <code>vendor/</code> folder is missing</h2>';
    echo '<p>Because this project is deployed via GitHub, Git ignored the <code>vendor/</code> directory (in <code>.gitignore</code>), and Vercel build does not execute <code>composer install</code>.</p>';
    echo '<p><strong>Fix:</strong> Commit your <code>vendor/</code> folder to Git or deploy via Vercel CLI (<code>npx vercel --prod</code>).</p>';
    exit;
}

try {
    require __DIR__.'/../public/index.php';
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<h2>Laravel Exception Caught:</h2>';
    echo '<h3>'.htmlspecialchars($e->getMessage()).'</h3>';
    echo '<pre>'.htmlspecialchars($e->getTraceAsString()).'</pre>';
}
