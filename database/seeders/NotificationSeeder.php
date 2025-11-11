<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // Helpful when reseeding on a DB that already has rows:
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Clean specific IDs to avoid duplicate key errors on reseed
            DB::table('notification_recipients')->whereIn('id', [1,2,3,4,6,7,8,9,10,11,12,13,14,15])->delete();
            DB::table('notification_rules')->whereIn('id', range(1,15))->delete();
            DB::table('notification_templates')->whereIn('id', [1,2,3,4,5])->delete();

            // 1) TEMPLATES
            DB::table('notification_templates')->insert([
                [
                    'id' => 1,
                    'name' => 'CR Created - Notify Requester',
                    'subject' => 'CR #{{cr_no}} has been created',
                    'body' => 'Dear {{first_name}}, <br><br>CR #{{cr_no}} has been created successfully.<br><br>You can review it here: <a href="{{cr_link}}">CR: #{{cr_no}}</a><br><br>Thank you',
                    'available_placeholders' => '["cr_no","first_name","cr_link"]',
                    'is_active' => 1,
                    'created_at' => '2025-10-30 20:42:59',
                    'updated_at' => '2025-10-30 20:42:59',
                ],
                [
                    'id' => 2,
                    'name' => 'CR Created - Notify Division Manager (Regular CR)',
                    'subject' => 'CR #{{cr_no}} - Awaiting Your Approval',
                    'body' => 'Dear {{division_manager_name}},<br><br>A Change Request (CR) with ID <strong>#<a href="{{cr_link}}">{{cr_no}}</a></strong> has been created and is currently <strong>awaiting your approval or rejection</strong> as the requester\'s Division Manager.<br><br><ul><li><strong>CR Subject:</strong> {{cr_title}}</li><li><strong>CR Description:</strong> {{cr_description}}</li><li><strong>Requester:</strong> {{requester_name}}</li><li><strong>Reference:</strong> CR ID #{{cr_no}}</li></ul><br><div style="margin: 25px 0;"><a href="{{approve_link}}" style="background-color: #4CAF50; color: white; padding: 10px 20px; margin-right: 10px; text-decoration: none; border-radius: 4px;">Approve </a> <a href="{{reject_link}}" style="background-color: #f44336; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Reject</a></div><br>Thank you in advance for your prompt action.<br><br><strong>Note:</strong> This is an automated message sent by the <strong>IT TMS System</strong>.<br><strong>Best regards,</strong><br><strong>TMS</strong>',
                    'available_placeholders' => '["cr_no","division_manager_name","cr_title","cr_description","requester_name","cr_link","approve_link","reject_link"]',
                    'is_active' => 1,
                    'created_at' => '2025-10-30 20:42:59',
                    'updated_at' => '2025-10-30 20:42:59',
                ],
                [
                    'id' => 3,
                    'name' => 'CR Created - Notify Division Manager (Promo)',
                    'subject' => 'Promo #{{cr_no}}',
                    'body' => 'Dear {{division_manager_name}},<br><br>Promo with ID <strong>#<a href="{{cr_link}}">{{cr_no}}</a></strong> has been created and is currently <ul><li><strong>Promo Subject:</strong> {{cr_title}}</li><li><strong>Promo Description:</strong> {{cr_description}}</li><li><strong>Requester:</strong> {{requester_name}}</li><li><strong>Reference:</strong> Promo ID #{{cr_no}}</li></ul><br><strong>Note:</strong> This is an automated message sent by the <strong>IT TMS System</strong>.<br><strong>Best regards,</strong><br><strong>TMS</strong>',
                    'available_placeholders' => '["cr_no","division_manager_name","cr_title","cr_description","requester_name","cr_link"]',
                    'is_active' => 1,
                    'created_at' => '2025-10-30 20:42:59',
                    'updated_at' => '2025-10-30 20:42:59',
                ],
                [
                    'id' => 4,
                    'name' => 'CR Status Updated - Notify Requester',
                    'subject' => 'CR #{{cr_no}} status has been changed',
                    'body' => 'Dear {{first_name}}, <br><br>CR #{{cr_no}} status has been changed from <strong>{{old_status}}</strong> to <strong>{{new_status}}</strong>.<br><br>CR Title: {{cr_title}}<br><br>You can review it here: <a href="{{cr_link}}">CR: #{{cr_no}}</a><br><br><strong>Note:</strong> This is an automated message sent by the <strong>IT TMS System</strong>.<br><strong>Best regards,</strong><br><strong>TMS</strong>',
                    'available_placeholders' => '["cr_no","first_name","old_status","new_status","cr_title","cr_link"]',
                    'is_active' => 1,
                    'created_at' => '2025-10-30 20:44:12',
                    'updated_at' => '2025-10-30 20:44:12',
                ],
                [
                    'id' => 5,
                    'name' => 'CR Status Updated - Notify Group',
                    'subject' => 'CR #{{cr_no}} status has been changed',
                    'body' => 'Dear {{group_name}},<br><br>CR #{{cr_no}} status has been changed from <strong>{{old_status}}</strong> to <strong>{{new_status}}</strong>.<br><br>CR Title: {{cr_title}}<br><br>You can review it here: <a href="{{cr_link}}">CR: #{{cr_no}}</a><br><br><strong>Note:</strong> This is an automated message sent by the <strong>IT TMS System</strong>.<br><strong>Best regards,</strong><br><strong>TMS</strong>',
                    'available_placeholders' => '["cr_no","group_name","old_status","new_status","cr_title","cr_link"]',
                    'is_active' => 1,
                    'created_at' => '2025-10-30 20:44:12',
                    'updated_at' => '2025-10-30 20:44:12',
                ],
            ]);

            // 2) RULES  (must exist before recipients)
            DB::table('notification_rules')->insert([
                ['id'=>1,'name'=>'CR Created - Notify Requester','event_class'=>'App\\Events\\ChangeRequestCreated','conditions'=>null,'template_id'=>1,'is_active'=>1,'priority'=>100,'created_at'=>'2025-10-30 20:42:59','updated_at'=>'2025-10-30 20:42:59'],
                ['id'=>2,'name'=>'CR Created - Notify Division Manager (Regular)','event_class'=>'App\\Events\\ChangeRequestCreated','conditions'=>'{"workflow_type_not":"9"}','template_id'=>2,'is_active'=>1,'priority'=>90,'created_at'=>'2025-10-30 20:42:59','updated_at'=>'2025-10-30 20:42:59'],
                ['id'=>3,'name'=>'CR Created - Notify Division Manager (Promo)','event_class'=>'App\\Events\\ChangeRequestCreated','conditions'=>'{"workflow_type":"9"}','template_id'=>3,'is_active'=>1,'priority'=>90,'created_at'=>'2025-10-30 20:42:59','updated_at'=>'2025-10-30 20:42:59'],
                ['id'=>4,'name'=>'CR Status - Business Validation','event_class'=>'App\\Events\\ChangeRequestStatusUpdated','conditions'=>'{"new_status_id":"18"}','template_id'=>4,'is_active'=>1,'priority'=>90,'created_at'=>'2025-10-30 20:44:12','updated_at'=>'2025-10-30 20:44:12'],
                ['id'=>5,'name'=>'CR Status - Notify Dev Team on Implementation','event_class'=>'App\\Events\\ChangeRequestStatusUpdated','conditions'=>'{"new_status_id":10}','template_id'=>4,'is_active'=>1,'priority'=>80,'created_at'=>'2025-10-30 20:44:12','updated_at'=>'2025-10-30 20:44:12'],
                ['id'=>6,'name'=>'CR Status - Pending Business Feedback','event_class'=>'App\\Events\\ChangeRequestStatusUpdated','conditions'=>'{"new_status_id":"79"}','template_id'=>4,'is_active'=>1,'priority'=>90,'created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>7,'name'=>'CR Status - Required Info','event_class'=>'App\\Events\\ChangeRequestStatusUpdated','conditions'=>'{"new_status_id":"31"}','template_id'=>4,'is_active'=>1,'priority'=>90,'created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>8,'name'=>'CR Status - Design Estimation','event_class'=>'App\\Events\\ChangeRequestStatusUpdated','conditions'=>'{"new_status_id":"3"}','template_id'=>4,'is_active'=>1,'priority'=>90,'created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>9,'name'=>'CR Status - Pending Desing','event_class'=>'App\\Events\\ChangeRequestStatusUpdated','conditions'=>'{"new_status_id":"7"}','template_id'=>4,'is_active'=>1,'priority'=>90,'created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>10,'name'=>'CR Status - Design In Progress','event_class'=>'App\\Events\\ChangeRequestStatusUpdated','conditions'=>'{"new_status_id":"15"}','template_id'=>4,'is_active'=>1,'priority'=>90,'created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>11,'name'=>'CR Status - Business Validation','event_class'=>'App\\Events\\ChangeRequestStatusUpdated','conditions'=>'{"new_status_id":"1000"}','template_id'=>4,'is_active'=>1,'priority'=>90,'created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>12,'name'=>'CR Status - Business Validation','event_class'=>'App\\Events\\ChangeRequestStatusUpdated','conditions'=>'{"new_status_id":"1000"}','template_id'=>4,'is_active'=>1,'priority'=>90,'created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>13,'name'=>'CR Status - Business Validation','event_class'=>'App\\Events\\ChangeRequestStatusUpdated','conditions'=>'{"new_status_id":"1000"}','template_id'=>4,'is_active'=>1,'priority'=>90,'created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>14,'name'=>'CR Status - Business Validation','event_class'=>'App\\Events\\ChangeRequestStatusUpdated','conditions'=>'{"new_status_id":"1000"}','template_id'=>4,'is_active'=>1,'priority'=>90,'created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>15,'name'=>'CR Status - Business Validation','event_class'=>'App\\Events\\ChangeRequestStatusUpdated','conditions'=>'{"new_status_id":"1000"}','template_id'=>4,'is_active'=>1,'priority'=>90,'created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
            ]);

            // 3) RECIPIENTS (after rules exist)
            DB::table('notification_recipients')->insert([
                ['id'=>1,'notification_rule_id'=>1,'channel'=>'to','recipient_type'=>'cr_creator','recipient_identifier'=>'','created_at'=>'2025-10-30 20:42:59','updated_at'=>'2025-10-30 20:42:59'],
                ['id'=>2,'notification_rule_id'=>2,'channel'=>'to','recipient_type'=>'division_manager','recipient_identifier'=>'','created_at'=>'2025-10-30 20:42:59','updated_at'=>'2025-10-30 20:42:59'],
                ['id'=>3,'notification_rule_id'=>3,'channel'=>'to','recipient_type'=>'division_manager','recipient_identifier'=>'','created_at'=>'2025-10-30 20:42:59','updated_at'=>'2025-10-30 20:42:59'],
                ['id'=>4,'notification_rule_id'=>4,'channel'=>'to','recipient_type'=>'cr_team','recipient_identifier'=>'','created_at'=>'2025-10-30 20:44:12','updated_at'=>'2025-10-30 20:44:12'],
                ['id'=>6,'notification_rule_id'=>6,'channel'=>'to','recipient_type'=>'cr_creator','recipient_identifier'=>'','created_at'=>'2025-10-30 20:44:12','updated_at'=>'2025-10-30 20:44:12'],
                ['id'=>7,'notification_rule_id'=>7,'channel'=>'to','recipient_type'=>'cr_member','recipient_identifier'=>'','created_at'=>'2025-10-30 20:44:12','updated_at'=>'2025-10-30 20:44:12'],
                ['id'=>8,'notification_rule_id'=>8,'channel'=>'to','recipient_type'=>'sa_team','recipient_identifier'=>'','created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>9,'notification_rule_id'=>9,'channel'=>'to','recipient_type'=>'designer','recipient_identifier'=>'','created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>10,'notification_rule_id'=>10,'channel'=>'to','recipient_type'=>'designer','recipient_identifier'=>'','created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>11,'notification_rule_id'=>11,'channel'=>'to','recipient_type'=>'cr_team','recipient_identifier'=>'','created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>12,'notification_rule_id'=>12,'channel'=>'to','recipient_type'=>'cr_team','recipient_identifier'=>'','created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>13,'notification_rule_id'=>13,'channel'=>'to','recipient_type'=>'cr_team','recipient_identifier'=>'','created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>14,'notification_rule_id'=>14,'channel'=>'to','recipient_type'=>'cr_team','recipient_identifier'=>'','created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
                ['id'=>15,'notification_rule_id'=>15,'channel'=>'to','recipient_type'=>'cr_team','recipient_identifier'=>'','created_at'=>'2025-10-30 11:38:53','updated_at'=>'2025-10-30 11:38:53'],
            ]);

            // Optional: department update exactly as in SQL
            DB::table('departments')->where('id', 4)->update([
                'name' => 'QC',
                'created_at' => null,
                'updated_at' => null,
            ]);

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        });
    }
}
