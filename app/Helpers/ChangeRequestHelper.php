<?php

use Illuminate\Support\Facades\Config;

if (!function_exists('status_matches')) {
    function status_matches($currentStatus, $statusKey)
    {
        $statuses = Config::get('change_request.status_ids');

        $normal = $statuses[$statusKey] ?? null;
        $kam = $statuses[$statusKey . '_kam'] ?? null;

        return $currentStatus == $normal || $currentStatus == $kam;
    }
}
