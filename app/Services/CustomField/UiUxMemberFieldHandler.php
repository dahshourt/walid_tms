<?php

namespace App\Services\CustomField;

use App\Models\Group;
use App\Models\User;
use App\Models\UserGroups;

class UiUxMemberFieldHandler
{
    /**
     * Handle the UI/UX Member custom field
     * 
     * @param array $field
     * @param mixed $value
     * @return array
     */
    public static function handle($field, $value = null)
    {
        // Get the ux.it group
        $uxItGroup = Group::where('title', 'ux.it')->first();
        
        if (!$uxItGroup) {
            return [
                'field' => $field,
                'value' => $value,
                'options' => [],
                'error' => 'UX/IT group not found'
            ];
        }
        
        // Get users in the ux.it group
        $users = User::whereHas('user_groups', function($query) use ($uxItGroup) {
            $query->where('group_id', $uxItGroup->id);
        })->where('active', 1)
          ->orderBy('name')
          ->pluck('name', 'id')
          ->toArray();
        
        // Add the options to the field
        $field['options'] = $users;
        
        return [
            'field' => $field,
            'value' => $value,
            'options' => $users
        ];
    }
}
