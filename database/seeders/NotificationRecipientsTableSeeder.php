<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationRecipientsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('notification_recipients')->insert([
            'notification_rule_id' => 2,
            'channel' => 'bcc',
            'recipient_type' => 'dm_bcc',
            'recipient_identifier' => ' ',
            'created_at' => '2025-11-18 13:19:59',
            'updated_at' => '2025-11-18 13:20:02',
        ]);
    }
}