<?php

use App\Models\change_request_statuse;
use Illuminate\Database\Eloquent\Builder;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $class = change_request_statuse::class;
    echo "Class: $class\n";
    $model = new change_request_statuse();
    echo "Model created.\n";

    $builder = $model->newQuery();
    echo "Builder created.\n";

    // Check if scopeActive is callable
    if (method_exists($model, 'scopeActive')) {
        echo "scopeActive exists on model.\n";
    } else {
        echo "scopeActive DOES NOT exist on model.\n";
    }

    try {
        $builder->active();
        echo "Builder->active() called successfully.\n";
    } catch (\Throwable $e) {
        echo "Error calling active(): " . $e->getMessage() . "\n";
    }

} catch (\Throwable $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}
