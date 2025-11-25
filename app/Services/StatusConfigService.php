<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatusConfigService
{
    /**
     * Load default status IDs (without suffix)
     */
    public static function loadStatusIds(): array
    {
        return self::mapStatuses('');
    }

    /**
     * Load KAM status IDs (with ' kam' suffix)
     */
    public static function loadStatusIdsKam(): array
    {
        return self::mapStatuses(' kam');
    }

    /**
     * Load status IDs by custom suffix
     */
    public static function loadStatusIdsBySuffix(string $suffix): array
    {
        return self::mapStatuses($suffix);
    }

    /**
     * Map status names to IDs from database
     * 
     * @param string $suffix The suffix to append to status names (e.g., ' kam', ' promo')
     * @return array Array of status key => status ID mappings
     */
    private static function mapStatuses(string $suffix): array
    {
        // Check if we're in a context where we can access the database
        if (!self::canAccessDatabase()) {
            Log::error('Cannot access database when loading statuses with suffix: ' . $suffix);
            return [];
        }

        $names = self::getStatusNameMappings();
        $result = [];
        $found = [];
        $notFound = [];

        try {
            foreach ($names as $key => $baseName) {
                $searchName = $baseName . $suffix;
                
                // Use DB facade directly with query builder (more reliable during bootstrap)
                $status = DB::table('statuses')
                    ->where('status_name', $searchName)
                    ->where('active', '1')
                    ->first();
                
                if ($status) {
                    $result[$key] = $status->id;
                    $found[] = $searchName;
                } else {
                    $notFound[] = $searchName;
                }
            }
            
            // Log the results
            if (!empty($notFound)) {
                Log::warning('The following statuses were not found in the database:', ['statuses' => $notFound]);
            }
            Log::info('Successfully loaded ' . count($found) . ' statuses with suffix: ' . $suffix);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Error loading statuses: ' . $e->getMessage(), [
                'exception' => $e,
                'suffix' => $suffix
            ]);
            return [];
        }
    }

    /**
     * Check if we can access the database
     */
    private static function canAccessDatabase(): bool
    {
        try {
            // Check if app is bound and database connection is available
            if (!function_exists('app') || !app()->bound('db')) {
                return false;
            }

            // Try a simple query to verify database is accessible
            DB::connection()->getPdo();
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the status name mappings
     * This defines which config keys map to which status names
     */
    private static function getStatusNameMappings(): array
    {
        $mappings = [
            'technical_estimation' => 'Technical estimation',
            'pending_implementation' => 'Pending implementation',
            'technical_implementation' => 'Technical Implementation',
            'pending_production_deployment' => 'Pending production Deployment',//'Pending production Deployment Promo',
            'production_deployment' => 'Deploy In Progress',
            'business_approval' => 'Business Approval',
            'business_approval_kam' => 'Business Approval',  // KAM specific
            'pending_cab' => 'Pending CAB',
            'pending_cab_kam' => 'Pending CAB',  // KAM specific
            'pending_cab_proceed' => 'Pending Test Cases Rework',
            'pending_cab_review' => 'Pending CAB',
            'set_kickoff' => 'confirmed',
            'cr_manager_review' => 'Review CD_CR',
            'design_phase' => 'Design Test Case Approval',
            'development_ready' => 'Pending Create Test Cases',
            'development_in_progress' => 'Business Test Case Approval',
            'testing_phase' => 'UAT In Progress',
            'uat_phase' => 'CR Production Deployment Pre -requisites',
            'business_test_case_approval' => 'Business Test Case Approval',
            'business_test_case_approval_kam' => 'Business Test Case Approval',  // KAM specific
            'business_uat_sign_off' => 'Business UAT Sign Off',
            'business_uat_sign_off_kam' => 'Business UAT Sign Off',  // KAM specific
            'pending_business' => 'Pending Business',
            'pending_business_kam' => 'Pending Business',  // KAM specific
            'pending_business_feedback' => 'Pending Business Feedback',
            'pending_business_feedback_kam' => 'Pending Business Feedback',  // KAM specific
            'approved_implementation_plan' => 'Approved Implementation Plan',
            'pending_cd_analysis' => 'Pending CD Analysis',
            'pending_stage_deployment_in_house' => 'Pending Stage Deployment',
            'pending_production_deployment_in_house' => 'Pending Production Deployment',
            'Reject' => 'Reject',
            'Reject_kam' => 'Reject',
            'Cancel' => 'Cancel',
            'Cancel_kam' => 'Cancel'
        ];

        return $mappings;
    }

    /**
     * Get a specific status ID by key and suffix
     * This method always queries the database directly
     */
    public static function getStatusId(string $key, string $suffix = ''): ?int
    {
        // Check if database is accessible
        if (!self::canAccessDatabase()) {
            return null;
        }

        // Get the status name for this key
        $mappings = self::getStatusNameMappings();
        if (!isset($mappings[$key])) {
            return null;
        }

        $searchName = $mappings[$key] . $suffix;

        try {
            $status = DB::table('statuses')
                ->where('status_name', $searchName)
                ->where('active', '1')
                ->first();

            return $status ? $status->id : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get all status IDs for multiple suffixes
     */
    public static function loadAllStatusVariations(array $suffixes = ['', ' kam']): array
    {
        $result = [];
        
        foreach ($suffixes as $suffix) {
            $key = $suffix === '' ? 'default' : trim($suffix);
            $result[$key] = self::mapStatuses($suffix);
        }
        
        return $result;
    }

    /**
     * Validate that all required status IDs are present
     */
    public static function validateStatusIds(string $suffix = ''): array
    {
        $statusIds = self::mapStatuses($suffix);
        $requiredKeys = array_keys(self::getStatusNameMappings());
        
        $missing = [];
        foreach ($requiredKeys as $key) {
            if (!isset($statusIds[$key])) {
                $missing[] = $key;
            }
        }
        
        return [
            'valid' => empty($missing),
            'missing_keys' => $missing,
            'found_count' => count($statusIds),
            'total_count' => count($requiredKeys),
        ];
    }

    /**
     * Get status name by key
     */
    public static function getStatusNameByKey(string $key): ?string
    {
        $mappings = self::getStatusNameMappings();
        return $mappings[$key] ?? null;
    }

    /**
     * Check if a status key exists in mappings
     */
    public static function hasStatusKey(string $key): bool
    {
        $mappings = self::getStatusNameMappings();
        return isset($mappings[$key]);
    }

    /**
     * Debug method to list all statuses containing 'kam' from the database
     */
    public static function debugKamStatuses()
    {
        try {
            $statuses = DB::table('statuses')
                ->where('status_name', 'like', '%kam%')
                ->where('active', '1')
                ->pluck('status_name', 'id')
                ->toArray();

            if (empty($statuses)) {
                return 'No statuses with "kam" found in the database.';
            }

            return $statuses;
        } catch (\Exception $e) {
            return 'Error fetching statuses: ' . $e->getMessage();
        }
    }
}