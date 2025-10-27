<?php

namespace App\Http\Repository\CustomField;

use App\Models\CustomField;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CustomFieldRepository
{
    /**
     * Get all custom fields with pagination
     *
     * @param bool $paginate
     * @return LengthAwarePaginator|Collection
     */
    public function getAllCustomFields(bool $paginate = false)
    {
        $query = CustomField::orderBy('id', 'desc');

        return $paginate ? $query->paginate(15) : $query->get();
    }

    /**
     * Find custom field by ID
     *
     * @param int $id
     * @return CustomField|null
     */
    public function findCustomField(int $id): ?CustomField
    {
        return CustomField::find($id);
    }

    /**
     * Create new custom field
     *
     * @param array $data
     * @return CustomField
     */
    public function createCustomField(array $data): CustomField
    {
        return CustomField::create($data);
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
        $customField = $this->findCustomField($id);

        if (!$customField) {
            return false;
        }

        return $customField->update($data);
    }

    /**
     * Toggle custom field status
     *
     * @param int $id
     * @return bool
     */
    public function toggleCustomFieldStatus(int $id): bool
    {
        $customField = $this->findCustomField($id);

        if (!$customField) {
            return false;
        }

        $customField->active = $customField->active === '1' ? '0' : '1';
        return $customField->save();
    }

    /**
     * Delete custom field
     *
     * @param int $id
     * @return bool
     */
    public function deleteCustomField(int $id): bool
    {
        $customField = $this->findCustomField($id);

        if (!$customField) {
            return false;
        }

        return $customField->delete();
    }

    /**
     * Get active custom fields
     *
     * @return Collection
     */
    public function getActiveCustomFields(): Collection
    {
        return CustomField::where('active', 1)->get();
    }

    /**
     * Search custom fields by name or label
     *
     * @param string $search
     * @param bool $paginate
     * @return LengthAwarePaginator|Collection
     */
    public function searchCustomFields(string $search, bool $paginate = false)
    {
        $query = CustomField::where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('label', 'like', "%{$search}%")
              ->orWhere('type', 'like', "%{$search}%");
        })->orderBy('id', 'desc');

        return $paginate ? $query->paginate(15) : $query->get();
    }

    /**
     * Get custom fields by type
     *
     * @param string $type
     * @return Collection
     */
    public function getCustomFieldsByType(string $type): Collection
    {
        return CustomField::where('type', $type)->get();
    }
}
