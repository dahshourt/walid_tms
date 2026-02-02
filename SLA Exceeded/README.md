# Time Manipulation Files - Collection

This folder contains files that directly manipulate time estimation fields in the TMS application.

## Collection Date
2026-01-29

## Files Copied (with preserved paths)

### Seeders
- **`database\seeders\FixChangeRequestSeeder.php`**
  - **Purpose**: Directly updates time fields for specific CRs
  - **Manipulates**: `start_develop_time`, `end_develop_time`, `start_test_time`, `end_test_time`
  - **Example**: Sets CR #31056 times to specific values

### Services - Time Estimation & Calculation
- **`app\Services\ChangeRequest\ChangeRequestEstimationService.php`**
  - **Purpose**: Calculates and sets time estimations for development, testing, design, and CR phases
  - **Manipulates**: 
    - `start_develop_time`, `end_develop_time`
    - `start_test_time`, `end_test_time`
    - `start_design_time`, `end_design_time`
    - `start_CR_time`, `end_CR_time`
    - `CR_duration`, `develop_duration`, `test_duration`, `design_duration`
  - **Key Methods**:
    - `calculateDevelopmentEstimation()` - Sets development start/end times
    - `calculateTestingEstimation()` - Sets testing start/end times
    - `calculateDesignEstimation()` - Sets design start/end times
    - `calculateCREstimation()` - Sets CR start/end times

- **`app\Services\ChangeRequest\ChangeRequestSchedulingService.php`**
  - **Purpose**: Schedules and reschedules CRs, managing time slots and conflicts
  - **Manipulates**:
    - `start_develop_time`, `end_develop_time`
    - `start_test_time`, `end_test_time`
  - **Key Methods**:
    - `schedulePromo()` - Schedules promo CRs with time assignments
    - `reschedulePromo()` - Reschedules existing CRs
    - `adjustTestSchedule()` - Adjusts test times based on development completion
    - `reorderTimes()` - Reorders CR times based on dependencies

### Repositories - Logging
- **`app\Http\Repository\Logs\LogRepository.php`**
  - **Purpose**: Logs changes to time fields
  - **Tracks**: Changes to `start_develop_time`, `end_develop_time`, `start_test_time`, `end_test_time`
  - **Key Methods**:
    - `logDurationWithTimes()` - Logs duration and time field changes

## Key Time Fields Manipulated

| Field Name | Description |
|------------|-------------|
| `start_develop_time` | Development phase start time |
| `end_develop_time` | Development phase end time |
| `start_test_time` | Testing phase start time |
| `end_test_time` | Testing phase end time |
| `start_design_time` | Design phase start time |
| `end_design_time` | Design phase end time |
| `start_CR_time` | Overall CR start time |
| `end_CR_time` | Overall CR end time |
| `CR_duration` | Total CR duration in hours |
| `develop_duration` | Development duration in hours |
| `test_duration` | Testing duration in hours |
| `design_duration` | Design duration in hours |

## Purpose
These files contain the core logic for setting, calculating, and manipulating time estimation fields for Change Requests.

## Related Investigation
This collection was created to investigate how time estimations are set and potentially reset in the system.
