<?php

namespace App\Services\CustomField;

use App\Http\Repository\CustomField\CustomFieldRepository;
use App\Models\CustomField;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CustomFieldService
{
    protected $customFieldRepository;

    public function __construct(CustomFieldRepository $customFieldRepository)
    {
        $this->customFieldRepository = $customFieldRepository;
    }

    /**
     * Get all custom fields
     *
     * @param bool $paginate
     * @return LengthAwarePaginator|Collection
     */
    public function getAllCustomFields(bool $paginate = false)
    {
        return $this->customFieldRepository->getAllCustomFields($paginate);
    }

    /**
     * Find custom field by ID
     *
     * @param int $id
     * @return CustomField|null
     */
    public function findCustomField(int $id): ?CustomField
    {
        return $this->customFieldRepository->findCustomField($id);
    }

    /**
     * Create new custom field
     *
     * @param array $data
     * @return CustomField
     */
    public function createCustomField(array $data): CustomField
    {
        // Set default values
        $data['active'] = $data['active'] ?? 1;

        // Clean and validate data
        $data = $this->prepareCustomFieldData($data);

        return $this->customFieldRepository->createCustomField($data);
    }

    /**
     * Update custom field
     *
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateCustomField(array $data, int $id): bool
    {
        // Clean and validate data
        $data = $this->prepareCustomFieldData($data);

        return $this->customFieldRepository->updateCustomField($data, $id);
    }

    /**
     * Toggle custom field status
     *
     * @param int $id
     * @return bool
     */
    public function updateCustomFieldStatus(int $id): bool
    {
        return $this->customFieldRepository->toggleCustomFieldStatus($id);
    }

    /**
     * Get input types from config
     *
     * @return array
     */
    public function getInputTypes(): array
    {
        return config('input_types', []);
    }

    /**
     * Prepare and clean custom field data
     *
     * @param array $data
     * @return array
     */
    private function prepareCustomFieldData(array $data): array
    {
        // Clean name field - remove spaces and convert to snake_case
        if (isset($data['name'])) {
            $data['name'] = strtolower(str_replace(' ', '_', trim($data['name'])));
        }

        // Ensure boolean fields are properly set
        $data['status'] = $data['active'];

        // Clean optional fields
        $data['class'] = isset($data['class']) ? trim($data['class']) : null;
        $data['default_value'] = isset($data['default_value']) ? trim($data['default_value']) : null;
        $data['related_table'] = isset($data['related_table']) ? trim($data['related_table']) : null;

        return $data;
    }

    /**
     * Validate custom field name uniqueness
     *
     * @param string $name
     * @param int|null $excludeId
     * @return bool
     */
    public function isNameUnique(string $name, ?int $excludeId = null): bool
    {
        $query = CustomField::where('name', $name);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->doesntExist();
    }

    /**
     * Get options from a database table
     *
     * @param string $tableName
     * @return array
     */
    public function getTableOptions(string $tableName): array
    {
        try {
            // Check if table exists
            if (!\Schema::hasTable($tableName)) {
                return [];
            }

            // Get table data - limit to 100 records for performance
            $data = \DB::table($tableName)->limit(100)->get();

            if ($data->isEmpty()) {
                return [];
            }

            // Convert to array format
            $options = [];
            foreach ($data as $row) {
                $rowArray = (array) $row;
                $options[] = $rowArray;
            }

            return $options;

        } catch (\Exception $e) {
            \Log::error('Error getting table options: ' . $e->getMessage());
            return [];
        }
    }
}
