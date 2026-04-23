<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test the fixed location mapping
$locations = ['Engg / INS'];
$count = App\Models\EngineeringItem::whereIn('location', $locations)->count();

echo "Engineering Items with location ['Engg / INS']: " . $count . PHP_EOL;

if ($count > 0) {
    echo "✓ Engineering users should now see their items correctly!\n";
} else {
    echo "✗ Still having issues...\n";
}
