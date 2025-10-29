<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CrCreationNotification extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Adding priority column
        if (! Schema::hasColumn('notification_rules', 'priority')) {
            DB::statement('ALTER TABLE `notification_rules` ADD COLUMN `priority` INT DEFAULT 0 AFTER `is_active`');
        }

        // CR Creation Notification Templates (creator and division manager)
        $templates = [
            [
                'name' => 'CR Created - Notify Requester',
                'subject' => 'CR #{{cr_no}} has been created',
                'body' => 'Dear {{first_name}}, <br><br>CR #{{cr_no}} has been created successfully.<br><br>You can review it here: <a href="{{cr_link}}">CR: #{{cr_no}}</a><br><br>Thank you',
                'available_placeholders' => json_encode(['cr_no', 'first_name', 'cr_link']),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CR Created - Notify Division Manager (Regular CR)',
                'subject' => 'CR #{{cr_no}} - Awaiting Your Approval',
                'body' => 'Dear {{division_manager_name}},<br><br>A Change Request (CR) with ID <strong>#<a href="{{cr_link}}">{{cr_no}}</a></strong> has been created and is currently <strong>awaiting your approval or rejection</strong> as the requester\'s Division Manager.<br><br><ul><li><strong>CR Subject:</strong> {{cr_title}}</li><li><strong>CR Description:</strong> {{cr_description}}</li><li><strong>Requester:</strong> {{requester_name}}</li><li><strong>Reference:</strong> CR ID #{{cr_no}}</li></ul><br><div style="margin: 25px 0;"><a href="{{approve_link}}" style="background-color: #4CAF50; color: white; padding: 10px 20px; margin-right: 10px; text-decoration: none; border-radius: 4px;">Approve </a> <a href="{{reject_link}}" style="background-color: #f44336; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Reject</a></div><br>Thank you in advance for your prompt action.<br><br><strong>Note:</strong> This is an automated message sent by the <strong>IT TMS System</strong>.<br><strong>Best regards,</strong><br><strong>TMS</strong>',
                'available_placeholders' => json_encode(['cr_no', 'division_manager_name', 'cr_title', 'cr_description', 'requester_name', 'cr_link', 'approve_link', 'reject_link']),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CR Created - Notify Division Manager (Promo)',
                'subject' => 'Promo #{{cr_no}}',
                'body' => 'Dear {{division_manager_name}},<br><br>Promo with ID <strong>#<a href="{{cr_link}}">{{cr_no}}</a></strong> has been created and is currently <ul><li><strong>Promo Subject:</strong> {{cr_title}}</li><li><strong>Promo Description:</strong> {{cr_description}}</li><li><strong>Requester:</strong> {{requester_name}}</li><li><strong>Reference:</strong> Promo ID #{{cr_no}}</li></ul><br><strong>Note:</strong> This is an automated message sent by the <strong>IT TMS System</strong>.<br><strong>Best regards,</strong><br><strong>TMS</strong>',
                'available_placeholders' => json_encode(['cr_no', 'division_manager_name', 'cr_title', 'cr_description', 'requester_name', 'cr_link']),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($templates as $template) {
            DB::table('notification_templates')->updateOrInsert(
                ['name' => $template['name']],
                $template
            );
        }

        // Insert Notification Rules
        $requesterTemplateId = DB::table('notification_templates')->where('name', 'CR Created - Notify Requester')->value('id');
        $dmRegularTemplateId = DB::table('notification_templates')->where('name', 'CR Created - Notify Division Manager (Regular CR)')->value('id');
        $dmPromoTemplateId = DB::table('notification_templates')->where('name', 'CR Created - Notify Division Manager (Promo)')->value('id');

        $rules = [
            [
                'name' => 'CR Created - Notify Requester',
                'event_class' => 'App\\Events\\ChangeRequestCreated',
                'conditions' => null,
                'template_id' => $requesterTemplateId,
                'is_active' => 1,
                'priority' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CR Created - Notify Division Manager (Regular)',
                'event_class' => 'App\\Events\\ChangeRequestCreated',
                'conditions' => json_encode(['workflow_type_not' => '9']),
                'template_id' => $dmRegularTemplateId,
                'is_active' => 1,
                'priority' => 90,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CR Created - Notify Division Manager (Promo)',
                'event_class' => 'App\\Events\\ChangeRequestCreated',
                'conditions' => json_encode(['workflow_type' => '9']),
                'template_id' => $dmPromoTemplateId,
                'is_active' => 1,
                'priority' => 90,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($rules as $rule) {
            DB::table('notification_rules')->updateOrInsert(
                ['name' => $rule['name']],
                $rule
            );
        }

        // Insert Notification Recipients
        $requesterRuleId = DB::table('notification_rules')->where('name', 'CR Created - Notify Requester')->value('id');
        $dmRegularRuleId = DB::table('notification_rules')->where('name', 'CR Created - Notify Division Manager (Regular)')->value('id');
        $dmPromoRuleId = DB::table('notification_rules')->where('name', 'CR Created - Notify Division Manager (Promo)')->value('id');

        $recipients = [
            [
                'notification_rule_id' => $requesterRuleId,
                'channel' => 'to',
                'recipient_type' => 'cr_creator',
                'recipient_identifier' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'notification_rule_id' => $dmRegularRuleId,
                'channel' => 'to',
                'recipient_type' => 'division_manager',
                'recipient_identifier' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'notification_rule_id' => $dmPromoRuleId,
                'channel' => 'to',
                'recipient_type' => 'division_manager',
                'recipient_identifier' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($recipients as $recipient) {
            DB::table('notification_recipients')->updateOrInsert(
                [
                    'notification_rule_id' => $recipient['notification_rule_id'],
                    'recipient_type' => $recipient['recipient_type'],
                ],
                $recipient
            );
        }

        echo "CR Creation Notification seeding completed successfully!\n";
    }
}
