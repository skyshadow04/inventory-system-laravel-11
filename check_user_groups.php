<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $users = App\Models\User::select('id', 'name', 'email', 'user_group')->get();
    
    echo "User Groups Distribution:\n";
    echo "==========================\n";
    
    $groupCounts = $users->groupBy('user_group')->map->count();
    foreach ($groupCounts as $group => $count) {
        echo $group ?? 'NULL' . ": $count users\n";
    }
    
    echo "\nAll Users:\n";
    echo "==========\n";
    foreach ($users as $user) {
        echo "ID: " . $user->id . " | Name: " . $user->name . " | Email: " . $user->email . " | Group: " . ($user->user_group ?? 'NULL') . "\n";
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
