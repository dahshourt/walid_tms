<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomFieldsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('custom_fields')->insert([
            [
                'type' => 'checkbox',
                'name' => 'need_ui_ux',
                'label' => 'Need UI/UX',
                'class' => 'form-check-input',
                'default_value' => '0',
                'related_table' => null,
             
                'active' => '1',
                'created_at' => '2025-12-30 09:59:19',
                'updated_at' => '2025-12-30 09:59:19',
            ],
            [
                'type' => 'select',
                'name' => 'ui_ux_member',
                'label' => 'UI/UX Member',
                'class' => 'form-control select2',
                'default_value' => null,
                'related_table' => 'user',
            
                'active' => '1',
                'created_at' => '2025-12-30 11:07:33',
                'updated_at' => '2025-12-30 12:11:12',
            ],
            [
                'type' => 'checkbox',
                'name' => 'need_bi',
                'label' => 'Need BI',
                'class' => 'form-check-input',
                'default_value' => '0',
                'related_table' => null,
               
                'active' => '1',
                'created_at' => '2025-12-31 17:46:03',
                'updated_at' => '2025-12-31 18:09:58',
            ],
            [
                'type' => 'input',
                'name' => 'ui_ux_estimation_duration',
                'label' => 'UI/UX Estimation Duration (Hours)',
                'class' => 'form-control',
                'default_value' => '0',
                'related_table' => null,
              
                'active' => '1',
                'created_at' => '2025-12-31 21:32:01',
                'updated_at' => '2025-12-31 21:39:36',
            ],
        ]);
    }
}
