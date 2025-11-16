<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TechCustomFieldTableSeeder extends Seeder
{
    
    public function run()
    {
        DB::table('custom_fields')->insert([
            'type' => 'select',
            'name' => 'tech_group_id',
            'label' => 'Assigned Technical Group',
            'class' => 'dropdown',
            'default_value' => null,
            'related_table' => 'groups',
            'active' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}