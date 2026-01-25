<?php

use App\Models\Change_request;
use Illuminate\Database\Eloquent\Builder;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing Change_request whereHas('RequestStatuses')...\n";

    $query = Change_request::whereHas('RequestStatuses', function ($q) {
        echo "Inside query closure.\n";
        echo "Model: " . get_class($q->getModel()) . "\n";
        $q->active();
        echo "called active()\n";
    });

    echo "Query constructed.\n";
    // $query->get(); // Don't need to execute, construction should trigger error if immediate, 
    // but actually scope is called effectively immediately when closure runs.

    // To trigger closure execution we might need toSql or get.
    $query->toSql();
    echo "Query generated.\n";

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
