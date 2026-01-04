<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "Opcache reset.\n";
}

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "Testing StatusConfigService...\n";

try {
    $pendingCabId = \App\Services\StatusConfigService::getStatusId('pending_cab');
    echo "StatusConfigService::getStatusId('pending_cab'): " . $pendingCabId . "\n";

    $pendingCabKamId = \App\Services\StatusConfigService::getStatusId('pending_cab', ' kam');
    echo "StatusConfigService::getStatusId('pending_cab', ' kam'): " . $pendingCabKamId . "\n";

    $configValue = config('change_request.status_ids.pending_cab');
    echo "config('change_request.status_ids.pending_cab'): " . ($configValue === null ? 'NULL' : $configValue) . "\n";

    echo "Full config dump:\n";
    print_r(config('change_request'));

    if ($pendingCabId > 0 && $configValue === null) {
        echo "SUCCESS: StatusConfigService is working and config is not loading DB values.\n";
    } else {
        echo "FAILURE: Something is wrong.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
