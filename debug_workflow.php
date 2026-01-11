<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ChangeRequest;
use App\Models\ChangeRequestStatus;
use App\Models\Status;

// Get a sample change request
$changeRequest = ChangeRequest::first();

if (!$changeRequest) {
    echo "No change requests found\n";
    exit;
}

echo "Change Request ID: " . $changeRequest->id . "\n";
echo "Current Status: " . $changeRequest->getCurrentStatus()?->new_status_id . "\n\n";

// Get all active statuses for this change request
$activeStatuses = ChangeRequestStatus::where('cr_id', $changeRequest->id)
    ->whereRaw('CAST(active AS CHAR) = ?', ['1'])
    ->with('status')
    ->get();

echo "Active Statuses:\n";
foreach ($activeStatuses as $status) {
    echo "- Status Name: " . $status->status->status_name . "\n";
    echo "  New Status ID: " . $status->new_status_id . "\n";
    echo "  Active: " . $status->active . "\n\n";
}

// Check parallel workflow statuses
$parallelWorkflowStatuses = [
    'Request Draft CR Doc',
    'Pending Agreed Scope Approval-SA', 
    'Pending Agreed Scope Approval-Vendor',
    'Pending Agreed Scope Approval-Business'
];

$joinStatus = Status::where('status_name', 'Pending Update Agreed Requirements')->first();

if ($joinStatus) {
    echo "Join Status ID: " . $joinStatus->id . "\n\n";
    
    echo "Checking parallel workflow completion:\n";
    foreach ($parallelWorkflowStatuses as $statusName) {
        $hasReachedJoin = ChangeRequestStatus::where('cr_id', $changeRequest->id)
            ->whereHas('status', function($query) use ($statusName) {
                $query->where('status_name', $statusName);
            })
            ->where('new_status_id', $joinStatus->id)
            ->whereRaw('CAST(active AS CHAR) = ?', ['1'])
            ->exists();
        
        echo "- {$statusName}: " . ($hasReachedJoin ? "✓ Reached join status" : "✗ Not reached join status") . "\n";
    }
}
