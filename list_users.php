<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = App\Models\User::select('id', 'name', 'email', 'user_group')->get();

foreach ($users as $user) {
    echo "ID: " . $user->id . " | Name: " . $user->name . " | Group: " . ($user->user_group ?? 'NULL') . "\n";
}
