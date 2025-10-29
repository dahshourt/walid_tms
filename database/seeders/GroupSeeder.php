<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groups')->insert([
            'title' => 'Analysis',
            'description' => 'Analysis',
            'active' => '1',
        ]);
        DB::table('groups')->insert([
            'title' => 'Development',
            'description' => 'Development',
            'active' => '1',
        ]);
        DB::table('groups')->insert([
            'title' => 'Design',
            'description' => 'Design',
            'active' => '1',
        ]);

        DB::table('groups')->insert([
            'title' => 'QC',
            'description' => 'QC',
            'active' => '1',
        ]);
        DB::table('groups')->insert([
            'title' => 'Deployment',
            'description' => 'Deployment',
            'active' => '1',
        ]);
        DB::table('groups')->insert([
            'title' => 'Application support',
            'description' => 'Application support',
            'active' => '1',
        ]);
        DB::table('groups')->insert([
            'title' => 'QA',
            'description' => 'QA',
            'active' => '1',
        ]);
        DB::table('groups')->insert([
            'title' => 'CR creator',
            'description' => 'CR creator',
            'active' => '1',
            'parent_id' => '1',
        ]);
        DB::table('groups')->insert([
            'title' => 'Design team',
            'description' => 'Design team',
            'active' => '1',
            'parent_id' => '3',
        ]);
        DB::table('groups')->insert([
            'title' => 'Development team',
            'description' => 'Development team',
            'active' => '1',
            'parent_id' => '2',
        ]);
        DB::table('groups')->insert([
            'title' => 'QC team',
            'description' => 'QC team',
            'active' => '1',
            'parent_id' => '4',
        ]);
    }
}
