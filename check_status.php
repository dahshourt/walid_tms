<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$statuses = DB::table('statuses')
    ->where('status_name', 'like', '%Pending UAT Test%')
    ->pluck('status_name');

echo "Found Statuses:\n";
foreach ($statuses as $name) {
    echo "- " . $name . "\n";
}
