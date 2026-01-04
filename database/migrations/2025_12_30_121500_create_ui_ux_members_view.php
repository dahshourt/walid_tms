<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUiUxMembersView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
        CREATE OR REPLACE VIEW ui_ux_members AS
        SELECT 
            u.id,
            u.name,
            u.email
        FROM 
            users u
        JOIN 
            user_groups ug ON u.id = ug.user_id
        JOIN 
            `groups` g ON ug.group_id = g.id
        WHERE 
            g.title = 'ux.it'
        ORDER BY 
            u.name ASC
        ";

        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS ui_ux_members');
    }
}
