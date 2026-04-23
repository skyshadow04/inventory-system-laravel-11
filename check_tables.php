<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$engCount = App\Models\EngineeringItem::count();
$appCount = App\Models\Item::count();

echo 'Engineering Items Count: ' . $engCount . PHP_EOL;
echo 'APP Items Count: ' . $appCount . PHP_EOL;

$engSample = App\Models\EngineeringItem::take(3)->get();
echo PHP_EOL . 'Engineering Items Sample:' . PHP_EOL;
foreach ($engSample as $item) {
    echo '- ' . $item->sr_number . ': ' . $item->item_description . ' (Location: ' . $item->location . ')' . PHP_EOL;
}

$appSample = App\Models\Item::take(3)->get();
echo PHP_EOL . 'APP Items Sample:' . PHP_EOL;
foreach ($appSample as $item) {
    echo '- ' . $item->sr_number . ': ' . $item->item_description . ' (Location: ' . $item->location . ')' . PHP_EOL;
}
