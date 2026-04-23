<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Set up a mock engineer user
$engineer = App\Models\User::where('user_group', 'Engineer')->first();

if (!$engineer) {
    $engineer = App\Models\User::find(3); // User ID 3 is an engineer
}

if ($engineer) {
    echo "Testing with Engineer User: " . $engineer->name . " (" . $engineer->email . ")\n";
    echo "User Group: " . $engineer->user_group . "\n\n";
    
    // Simulate the controller logic
    $user = $engineer;
    $userGroup = $user->user_group ?? 'APP';
    echo "Detected User Group: " . $userGroup . "\n\n";
    
    $groupModelMapping = [
        'APP' => [App\Models\Item::class, ['APP']],
        'Engineering' => [App\Models\EngineeringItem::class, ['Engg / INS']],
        'Mechanical' => [App\Models\MechanicalItem::class, ['ENGG / MEC']],
        'Operations' => [App\Models\OperationItem::class, ['OPTNS']],
    ];
    
    [$itemModel, $allowedLocations] = $groupModelMapping[$userGroup] ?? [App\Models\Item::class, ['APP']];
    
    echo "Mapped Model: " . $itemModel . "\n";
    echo "Allowed Locations: " . implode(', ', $allowedLocations) . "\n\n";
    
    $items = $itemModel::whereIn('location', $allowedLocations)->limit(5)->get();
    
    echo "Items retrieved: " . count($items) . "\n";
    if (count($items) > 0) {
        echo "\nFirst 5 items:\n";
        foreach ($items as $item) {
            echo "- " . $item->sr_number . ": " . $item->item_description . " (Location: " . $item->location . ")\n";
        }
    }
} else {
    echo "No engineer user found in database\n";
}
