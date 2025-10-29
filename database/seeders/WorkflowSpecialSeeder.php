<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class WorkflowSpecialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('workflow_special')->insert([
            'no_need_desgin' => 1,
            'not_testable' => 1,
            'workflow_type_id' => 3,
            'from_status_id' => 3,
            'to_workflow_id' => 47,
        ]);
        DB::table('workflow_special')->insert([
            'no_need_desgin' => 1,
            'not_testable' => 1,
            'workflow_type_id' => 3,
            'from_status_id' => 4,
            'to_workflow_id' => 47,
        ]);
        DB::table('workflow_special')->insert([
            'no_need_desgin' => 1,
            'not_testable' => 1,
            'workflow_type_id' => 3,
            'from_status_id' => 6,
            'to_workflow_id' => 47,
        ]);
    }
}
