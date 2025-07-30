<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Template Purchases:\n";

$purchases = App\Models\TemplatePurchase::latest()->take(5)->get();

foreach($purchases as $purchase) {
    echo "ID: {$purchase->id}, User: {$purchase->user_id}, Template: {$purchase->template_id}, Gateway: {$purchase->payment_gateway_id}, Status: {$purchase->status}\n";
}

echo "\nChecking if Template exists:\n";
$templates = App\Models\Template::where('id', 1)->first();
if($templates) {
    echo "Template 1 exists: {$templates->name}\n";
} else {
    echo "Template 1 does not exist\n";
}
