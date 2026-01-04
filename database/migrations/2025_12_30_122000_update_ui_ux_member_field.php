<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateUiUxMemberField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update the custom field to use the view
        DB::table('custom_fields')
            ->where('name', 'ui_ux_member')
            ->update([
                'related_table' => 'ui_ux_members',
                'related_condition' => null, // Clear the condition since we're using a view
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Get the ux.it group ID
        $uxItGroup = DB::table('groups')->where('title', 'ux.it')->first();
        
        if ($uxItGroup) {
            // Revert to the previous configuration
            DB::table('custom_fields')
                ->where('name', 'ui_ux_member')
                ->update([
                    'related_table' => 'user_groups',
                    'related_condition' => json_encode([
                        'group_id' => $uxItGroup->id,
                        'join' => [
                            'table' => 'users',
                            'first' => 'user_groups.user_id',
                            'operator' => '=',
                            'second' => 'users.id'
                        ]
                    ]),
                    'updated_at' => now(),
                ]);
        }
    }
}
