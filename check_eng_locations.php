<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Engineering Items Locations:\n";
$locations = App\Models\EngineeringItem::whereNotNull('location')->distinct()->pluck('location')->toArray();
foreach ($locations as $loc) {
    echo "- '" . $loc . "'\n";
}

echo "\nEngineering Items by Location:\n";
foreach ($locations as $loc) {
    $count = App\Models\EngineeringItem::where('location', $loc)->count();
    echo "Location '" . $loc . "': " . $count . " items\n";
}

echo "\nTesting location filter ['Engg', 'INS']:\n";
$count = App\Models\EngineeringItem::whereIn('location', ['Engg', 'INS'])->count();
echo "Result: " . $count . " items\n";

echo "\nTesting location filter ['Engg / INS']:\n";
$count = App\Models\EngineeringItem::whereIn('location', ['Engg / INS'])->count();
echo "Result: " . $count . " items\n";
