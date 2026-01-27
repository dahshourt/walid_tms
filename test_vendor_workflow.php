<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Services\ChangeRequest\ChangeRequestStatusService;
use App\Models\Change_request;
use App\Models\Change_request_statuse;

echo "=== Vendor Workflow Test ===" . PHP_EOL;

// Test 1: Check if we have vendor workflow CRs
echo PHP_EOL . "1. Checking for vendor workflow CRs..." . PHP_EOL;
$vendorCRs = Change_request::whereIn('workflow_type_id', [2, 5])
    ->limit(5)
    ->get(['id', 'cr_no', 'workflow_type_id']);

foreach($vendorCRs as $cr) {
    echo "CR ID: {$cr->id}, CR No: {$cr->cr_no}, Workflow Type: {$cr->workflow_type_id}" . PHP_EOL;
}

// Test 2: Check status IDs
echo PHP_EOL . "2. Status IDs used in the logic:" . PHP_EOL;
$statusIds = [
    'Pending Create Agreed Scope' => 291,
    'Pending Agreed Scope Approval-SA' => 293,
    'Pending Agreed Scope Approval-Vendor' => 294,
    'Pending Agreed Scope Approval-Business' => 295,
];

foreach($statusIds as $name => $id) {
    $status = DB::table('statuses')->where('id', $id)->first();
    echo "{$name}: {$id} - " . ($status ? $status->status_name : 'NOT FOUND') . PHP_EOL;
}

// Test 3: Check if any CR has the approval statuses
echo PHP_EOL . "3. Checking CRs with approval statuses..." . PHP_EOL;
$approvalStatusIds = [293, 294, 295];
$crsWithApprovals = Change_request_statuse::whereIn('new_status_id', $approvalStatusIds)
    ->join('change_request', 'change_request_statuses.cr_id', '=', 'change_request.id')
    ->whereIn('change_request.workflow_type_id', [2, 5])
    ->limit(5)
    ->get(['change_request.id', 'change_request.cr_no', 'change_request.workflow_type_id', 'change_request_statuses.new_status_id', 'change_request_statuses.active']);

foreach($crsWithApprovals as $crStatus) {
    $statusName = DB::table('statuses')->where('id', $crStatus->new_status_id)->value('status_name');
    echo "CR ID: {$crStatus->id}, CR No: {$crStatus->cr_no}, Status: {$crStatus->new_status_id} ({$statusName}), Active: {$crStatus->active}" . PHP_EOL;
}

// Test 4: Check for completed statuses (active=2)
echo PHP_EOL . "4. Checking for completed statuses (active=2)..." . PHP_EOL;
$completedStatuses = Change_request_statuse::where('active', '2')
    ->join('change_request', 'change_request_statuses.cr_id', '=', 'change_request.id')
    ->whereIn('change_request.workflow_type_id', [2, 5])
    ->limit(5)
    ->get(['change_request.id', 'change_request.cr_no', 'change_request_statuses.id as status_id', 'change_request_statuses.new_status_id', 'change_request_statuses.created_at']);

foreach($completedStatuses as $status) {
    $statusName = DB::table('statuses')->where('id', $status->new_status_id)->value('status_name');
    echo "CR ID: {$status->id}, CR No: {$status->cr_no}, Status ID: {$status->status_id}, Status: {$status->new_status_id} ({$statusName}), Created: {$status->created_at}" . PHP_EOL;
}

echo PHP_EOL . "=== Test Complete ===" . PHP_EOL;
echo "The vendor workflow logic has been implemented in ChangeRequestStatusService." . PHP_EOL;
echo "When a vendor workflow CR transitions from any approval status (293, 294, 295) to 'Pending Create Agreed Scope' (291)," . PHP_EOL;
echo "the system will:" . PHP_EOL;
echo "1. Find the last completed status (active=2) for that CR" . PHP_EOL;
echo "2. Reinsert it as an active status (active=1)" . PHP_EOL;
echo "3. Update all other active statuses (active=1) to completed (active=2) except the newly inserted one" . PHP_EOL;
