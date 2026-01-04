<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FilterUxItUsersForUiUxMemberField extends Migration
{
    public function up()
    {
        // Get the ID of the "ux.it" group
        $uxItGroup = DB::table('groups')
            ->where('title', 'ux.it')
            ->first();

        if ($uxItGroup) {
            // Update the custom field to only show users from the ux.it group
            DB::table('custom_fields')
                ->where('name', 'ui_ux_member')
                ->update([
                    'options' => json_encode([
                        'source' => 'users',
                        'group_id' => $uxItGroup->id
                    ]),
                    'updated_at' => now()
                ]);
        }
    }

    public function down()
    {
        // Revert back to default behavior if needed
        DB::table('custom_fields')
            ->where('name', 'ui_ux_member')
            ->update([
                'options' => json_encode(['source' => 'users']),
                'updated_at' => now()
            ]);
    }
}