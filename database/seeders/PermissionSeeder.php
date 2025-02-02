<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '1',
        ]);
        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '2',
        ]);
        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '3',
        ]);
        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '4',
        ]);

        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '5',
        ]);
        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '6',
        ]);
        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '7',
        ]);
        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '8',
        ]);
        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '9',
        ]);
        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '10',
        ]);
        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '11',
        ]);
        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '12',
        ]);
        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '13',
        ]);
        DB::table('permissions')->insert([
             'group_id' => '1',
            'module_rule_id' => '14',
        ]);
        DB::table('permissions')->insert([
            'group_id' => '1',
           'module_rule_id' => '18',
       ]);
        DB::table('permissions')->insert([
        'group_id' => '1',
       'module_rule_id' => '19',
   ]);
        DB::table('permissions')->insert([
    'group_id' => '1',
   'module_rule_id' => '17',
]);
        DB::table('permissions')->insert([
'group_id' => '1',
'module_rule_id' => '16',
]);
        DB::table('permissions')->insert([
    'group_id' => '1',
    'module_rule_id' => '17',
    ]);
    DB::table('permissions')->insert([
        'group_id' => '1',
        'module_rule_id' => '22',
        ]);
                DB::table('permissions')->insert([
            'group_id' => '1',
            'module_rule_id' => '21',
            ]);


            
            DB::table('permissions')->insert([
                'group_id' => '1',
                'module_rule_id' => '23',
                ]);
                DB::table('permissions')->insert([
                    'group_id' => '1',
                    'module_rule_id' => '24',
                    ]);
                    DB::table('permissions')->insert([
                        'group_id' => '1',
                        'module_rule_id' => '25',
                        ]);
                        DB::table('permissions')->insert([
                            'group_id' => '1',
                            'module_rule_id' => '26',
                            ]);
    }
}
