<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CrUpdateNotification extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure priority column exists
        if (! Schema::hasColumn('notification_rules', 'priority')) {
            Schema::table('notification_rules', function ($table) {
                $table->integer('priority')->default(0)->after('is_active');
            });
        }

        // CR Status Update Notification Templates
        $templates = [
            [
                'name' => 'CR Status Updated - Notify Requester',
                'subject' => 'CR #{{cr_no}} status has been changed',
                'body' => 'Dear {{first_name}}, <br><br>CR #{{cr_no}} status has been changed from <strong>{{old_status}}</strong> to <strong>{{new_status}}</strong>.<br><br>CR Title: {{cr_title}}<br><br>You can review it here: <a href="{{cr_link}}">CR: #{{cr_no}}</a><br><br><strong>Note:</strong> This is an automated message sent by the <strong>IT TMS System</strong>.<br><strong>Best regards,</strong><br><strong>TMS</strong>',
                'available_placeholders' => json_encode([
                    'cr_no', 'first_name', 'old_status', 'new_status',
                    'cr_title', 'cr_link',
                ]),
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CR Status Updated - Notify Group',
                'subject' => 'CR #{{cr_no}} status has been changed',
                'body' => 'Dear {{group_name}},<br><br>CR #{{cr_no}} status has been changed from <strong>{{old_status}}</strong> to <strong>{{new_status}}</strong>.<br><br>CR Title: {{cr_title}}<br><br>You can review it here: <a href="{{cr_link}}">CR: #{{cr_no}}</a><br><br><strong>Note:</strong> This is an automated message sent by the <strong>IT TMS System</strong>.<br><strong>Best regards,</strong><br><strong>TMS</strong>',
                'available_placeholders' => json_encode([
                    'cr_no', 'group_name', 'old_status', 'new_status',
                    'cr_title', 'cr_link',
                ]),
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

        // Get template IDs
        $requesterTemplateId = DB::table('notification_templates')
            ->where('name', 'CR Status Updated - Notify Requester')
            ->value('id');

        $groupTemplateId = DB::table('notification_templates')
            ->where('name', 'CR Status Updated - Notify Group')
            ->value('id');

        // Notification Rules - Examples
        $rules = [
            // Example 1: Specific status transition notification
            // When moved to "Pending CD Analysis" (101)
            // Send to requester
            [
                'name' => 'CR Status - Pending CD Analysis',
                'event_class' => 'App\\Events\\ChangeRequestStatusUpdated',
                'conditions' => json_encode([
                    'new_status_id' => '138',
                ]),
                'template_id' => $requesterTemplateId,
                'is_active' => 1,
                'priority' => 90,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Test Example
            [
                'name' => 'CR Status - Notify Dev Team on Implementation',
                'event_class' => 'App\\Events\\ChangeRequestStatusUpdated',
                'conditions' => json_encode([
                    'new_status_id' => config('change_request.status_ids.technical_implementation', '50'),
                ]),
                'template_id' => $groupTemplateId,
                'is_active' => 1,
                'priority' => 80,
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

        // Get rule IDs
        $requesterRuleId = DB::table('notification_rules')
            ->where('name', 'CR Status Updated - Notify Requester')
            ->value('id');

        $groupRuleId = DB::table('notification_rules')
            ->where('name', 'CR Status Updated - Notify Group')
            ->value('id');

        // Notification Recipients
        // Get the new rule IDs after insert
        $pendingCDAnalysisRuleId = DB::table('notification_rules')
            ->where('name', 'CR Status - Pending CD Analysis')
            ->value('id');

        $devTeamRuleId = DB::table('notification_rules')
            ->where('name', 'CR Status - Notify Dev Team on Implementation')
            ->value('id');

        $recipients = [
            // Example 1 Recipients: Notify cr_creator (requester)

            [
                'notification_rule_id' => $pendingCDAnalysisRuleId,
                'recipient_type' => 'assigned_group',
                'channel' => 'to',
                'recipient_identifier' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'notification_rule_id' => $pendingCDAnalysisRuleId,
                'recipient_type' => 'cr_creator',
                'channel' => 'to',
                'recipient_identifier' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Example 2 Recipients: Notify assigned_group (dev team)
            [
                'notification_rule_id' => $devTeamRuleId,
                'recipient_type' => 'assigned_group',
                'channel' => 'to',
                'recipient_identifier' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Example 2: Also notify developer if assigned
            [
                'notification_rule_id' => $devTeamRuleId,
                'recipient_type' => 'developer',
                'channel' => 'to',
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
                    'channel' => $recipient['channel'],
                ],
                $recipient
            );
        }

        $this->command->info('CR Update Notification seeding completed successfully!');
    }
}
