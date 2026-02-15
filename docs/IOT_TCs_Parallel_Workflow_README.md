# IOT TCs Review Parallel Workflow - Implementation Changes

## Overview
This document describes the changes needed to enable parallel workflow for IOT TCs Review statuses.

**Workflow:**
```
Vendor Internal Test (workflow 9078)
    ├── Pending IOT TCs Review QC  → IOT TCs Review QC     ─┐
    ├── Pending IOT TCs Review SA  → IOT TCs Review vendor  ─┤── merge → IOT In Progress
    └── Pending IOT TCs Review     → (next status)          ─┘
```

**Problem:** When transitioning one IOT TCs branch (e.g., QC → IOT TCs Review QC), the normal `handleDependentStatuses()` deactivates the sibling branches (SA and Review), blocking them.

**Solution:** New `IotTcsFlowService` + 7 targeted changes to `ChangeRequestStatusService.php`.

---

## Files

### 1. NEW: `app/Services/ChangeRequest/SpecialFlows/IotTcsFlowService.php`
✅ Already in this branch.

### 2. MODIFY: `app/Services/ChangeRequest/ChangeRequestStatusService.php`

Apply 7 changes described below.

---

## CHANGE 1: Add import (after `use InvalidArgumentException;`)

```php
use App\Services\ChangeRequest\SpecialFlows\IotTcsFlowService;
```

## CHANGE 2: Add property (after `$dependencyService`)

```php
private ?IotTcsFlowService $iotTcsFlowService = null;
```

## CHANGE 3: Initialize in `__construct()` (after mailController)

```php
$this->iotTcsFlowService = new IotTcsFlowService();
```

## CHANGE 4: In `processStatusUpdate()` - after UatPromoFlowService block

```php
try {
    $iotResult = $this->iotTcsFlowService->handleIOTTcsTransition($changeRequest->id, $statusData);
    if ($iotResult !== null) {
        $this->active_flag = $iotResult;
        Log::info('IOT TCs parallel workflow handled', [
            'cr_id' => $changeRequest->id, 'active_flag' => $iotResult
        ]);
    }
} catch (\Throwable $e) {
    Log::error('Error in IotTcsFlowService', [
        'cr_id' => $changeRequest->id, 'error' => $e->getMessage()
    ]);
}
```

## CHANGE 5: In `handleDependentStatuses()` - add IOT TCs branch

After `$currentIsWorkflowA = ...;` add:
```php
$currentIsIOTTcs = $this->iotTcsFlowService->isIOTTcsPendingStatus($currentStatus->new_status_id);
```

Then add `elseif ($currentIsIOTTcs)` block between Workflow A and normal mode.
Also add IOT TCs preservation in the normal mode's else block.

See IMPLEMENTATION_GUIDE.md for full code.

## CHANGE 6: In `determineActiveStatus()` - after Workflow A check

Add IOT TCs priority check that forces `active=1` for IOT TCs transitions,
with merge point logic for "IOT In Progress".

## CHANGE 7: In `activatePendingMergeStatus()` - at end

```php
try {
    $this->iotTcsFlowService->activateMergeIfReady($crId, $statusData);
} catch (\Throwable $e) {
    Log::error('Error in IOT TCs merge activation', [
        'cr_id' => $crId, 'error' => $e->getMessage()
    ]);
}
```

## Required Status Names in DB
- Pending IOT TCs Review QC
- Pending IOT TCs Review SA
- Pending IOT TCs Review
- IOT TCs Review QC
- IOT TCs Review vendor
- IOT In progress
- Vendor Internal Test
