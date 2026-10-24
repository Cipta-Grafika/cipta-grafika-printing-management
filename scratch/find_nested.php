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

preg_match_all('/<script[\s\S]*?<\/script>/i', $html, $matches);
foreach ($matches[0] as $i => $block) {
    // Check if block has another <script inside it (excluding the opening one)
    $inner = substr($block, 7);
    if (stripos($inner, '<script') !== false) {
        echo "NESTED SCRIPT IN BLOCK #{$i}:\n";
        echo substr($block, 0, 500) . "\n...\n";
    }
}

