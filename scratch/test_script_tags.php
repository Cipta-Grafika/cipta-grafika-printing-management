<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/product-details', 'GET', [
    'type' => 'display',
    'id' => 42
]);

$controller = new App\Http\Controllers\user\CatalogCT();
$response = $controller->productDetails($request);
$html = $response->render();

// Check if there are any nested script tags <script.*<script
if (preg_match('/<script[^>]*>[\s\S]*?<script/i', $html)) {
    echo "ERROR: Nested script tags detected!\n";
} else {
    echo "CLEAN: No nested script tags found.\n";
}

// Find all script tags count
$openTags = substr_count($html, '<script');
$closeTags = substr_count($html, '</script>');
echo "Open <script> tags: {$openTags} | Close </script> tags: {$closeTags}\n";

if ($openTags === $closeTags) {
    echo "Script tags match perfectly!\n";
} else {
    echo "MISMATCH in script tags!\n";
}

