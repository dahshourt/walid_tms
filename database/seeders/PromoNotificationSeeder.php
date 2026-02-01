<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromoNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed notification templates
        $this->seedNotificationTemplates();
        
        // Seed notification rules
        $this->seedNotificationRules();
        
        // Seed notification recipients
        $this->seedNotificationRecipients();
    }

    /**
     * Seed notification templates
     */
    private function seedNotificationTemplates()
    {
        $templates = [
            [
                'id' => 7,
                'name' => 'Promo Created - Notify Requester',
                'subject' => '[TMS] Promo #{{cr_no}} has been created',
                'body' => '<p>Dear {{first_name}},</p><p>Promo #{{cr_no}} has been <b>created successfully</b>.</p><p><b>Promo Title:</b> {{cr_title}}<br></p><p>You can review it here: <a href="{{cr_link}}"><b>View Promo #{{cr_no}}</b></a></p><hr><p><i><b>Note:</b> This is an automated message sent by the IT TMS System.</i></p><p><b>Best regards,</b><br><b>TMS</b></p>',
                'available_placeholders' => '["cr_no", "group_name", "old_status", "new_status", "cr_title", "cr_link"]',
                'is_active' => 1,
                'created_at' => '2025-10-30 11:38:53',
                'updated_at' => '2025-11-19 16:43:31',
            ],
            [
                'id' => 9,
                'name' => 'Promo Assignment',
                'subject' => '[TMS] Promo #{{cr_no}} has been created and assigned to You',
                'body' => '<p>Dears,</p><p>Promo #{{cr_no}} has been <b>created</b> and&nbsp;<b>assigned</b> to you for your action.</p><p><b>Promo Title:</b> {{cr_title}}<br><b>Promo Status:</b> {{current_status}}</p><p>You can review it here: <a href="{{cr_link}}"><b>View Promo #{{cr_no}}</b></a></p><hr><p><i><b>Note:</b> This is an automated message sent by the IT TMS System.</i></p><p><b>Best regards,</b><br><b>TMS</b></p>',
                'available_placeholders' => '["cr_no", "group_name", "old_status", "new_status", "cr_title", "cr_link"]',
                'is_active' => 1,
                'created_at' => '2025-10-30 11:38:53',
                'updated_at' => '2025-11-20 09:58:56',
            ],
            [
                'id' => 10,
                'name' => 'Promo Status Updated (notify team or user)',
                'subject' => '[TMS] Promo #{{cr_no}} status has been changed',
                'body' => '<p>Dear {{first_name}},</p><p>The status of the Promo #{{cr_no}} has been updated from&nbsp;<span style="font-weight: 600;">{{old_status}}</span>&nbsp;to&nbsp;<span style="font-weight: 600;">{{new_status}}</span>.</p><p><span style="font-weight: 600;">Promo Title:</span>&nbsp;{{cr_title}}</p><p>You can review it here:&nbsp;<a href="{{cr_link}}"><span style="font-weight: 600;">View Promo #{{cr_no}}</span></a></p><hr><p><i><span style="font-weight: 600;">Note:</span>&nbsp;This is an automated message sent by the IT TMS System.</i></p><p><span style="font-weight: 600;">Best regards,</span><br><span style="font-weight: 600;">TMS</span></p>',
                'available_placeholders' => '["cr_no", "group_name", "old_status", "new_status", "cr_title", "cr_link"]',
                'is_active' => 1,
                'created_at' => '2025-10-30 11:38:53',
                'updated_at' => '2025-11-19 16:53:51',
            ],
            [
                'id' => 11,
                'name' => 'Promo Status Updated (notify team and user)',
                'subject' => '[TMS] Promo #{{cr_no}} status has been changed',
                'body' => '<p>Dears,</p><p>The status of the Promo #{{cr_no}} has been updated from&nbsp;<span style="font-weight: 600;">{{old_status}}</span>&nbsp;to&nbsp;<span style="font-weight: 600;">{{new_status}}</span>.</p><p><span style="font-weight: 600;">Promo Title:</span>&nbsp;{{cr_title}}</p><p>You can review it here:&nbsp;<a href="{{cr_link}}"><span style="font-weight: 600;">View Promo #{{cr_no}}</span></a></p><hr><p><i><span style="font-weight: 600;">Note:</span>&nbsp;This is an automated message sent by the IT TMS System.</i></p><p><span style="font-weight: 600;">Best regards,</span><br><span style="font-weight: 600;">TMS</span></p>',
                'available_placeholders' => '["cr_no", "group_name", "old_status", "new_status", "cr_title", "cr_link"]',
                'is_active' => 1,
                'created_at' => '2025-10-30 11:38:53',
                'updated_at' => '2025-11-19 17:04:27',
            ],
            [
                'id' => 12,
                'name' => 'Promo Status Updated (custom template cr date)',
                'subject' => '[TMS] Promo #{{cr_no}} status has been changed',
                'body' => '<p>Dears,</p><p>The status of the Promo #{{cr_no}} has been updated from&nbsp;<span style="font-weight: 600;">{{old_status}}</span>&nbsp;to&nbsp;<span style="font-weight: 600;">{{new_status}}</span>.</p><p><span style="font-weight: 600;">Promo Title:</span>&nbsp;{{cr_title}}</p><p><b>Promo Start Date:</b> {{start_cr_date}}</p><p>You can review it here:&nbsp;<a href="{{cr_link}}"><span style="font-weight: 600;">View Promo #{{cr_no}}</span></a></p><hr><p><i><span style="font-weight: 600;">Note:</span>&nbsp;This is an automated message sent by the IT TMS System.</i></p><p><span style="font-weight: 600;">Best regards,</span><br><span style="font-weight: 600;">TMS</span></p>',
                'available_placeholders' => '["cr_no", "group_name", "old_status", "new_status", "cr_title", "cr_link"]',
                'is_active' => 1,
                'created_at' => '2025-10-30 11:38:53',
                'updated_at' => '2025-11-19 17:13:12',
            ],
            [
                'id' => 13,
                'name' => 'Promo Status Updated (custom template sa date)',
                'subject' => '[TMS] Promo #{{cr_no}} status has been changed',
                'body' => '<p>Dears,</p><p>The status of the Promo #{{cr_no}} has been updated from&nbsp;<span style="font-weight: 600;">{{old_status}}</span>&nbsp;to&nbsp;<span style="font-weight: 600;">{{new_status}}</span>.</p><p><span style="font-weight: 600;">Promo Title:</span>&nbsp;{{cr_title}}</p><p><span style="font-weight: 600;">Promo Start SA Date:</span>&nbsp;{{start_sa_date}}</p><p>You can review it here:&nbsp;<a href="{{cr_link}}"><span style="font-weight: 600;">View Promo #{{cr_no}}</span></a></p><hr><p><i><span style="font-weight: 600;">Note:</span>&nbsp;This is an automated message sent by the IT TMS System.</i></p><p><span style="font-weight: 600;">Best regards,</span><br><span style="font-weight: 600;">TMS</span></p>',
                'available_placeholders' => '["cr_no", "group_name", "old_status", "new_status", "cr_title", "cr_link"]',
                'is_active' => 1,
                'created_at' => '2025-10-30 11:38:53',
                'updated_at' => '2025-11-19 17:14:42',
            ],
            [
                'id' => 14,
                'name' => 'Promo Status Updated (Kickoff meeting)',
                'subject' => '[TMS] Promo #{{cr_no}} status has been changed',
                'body' => '<p>Dears,</p><p>The status of Promo #{{cr_no}} has been updated from&nbsp;<span style="font-weight: 600;">{{old_status}}</span>&nbsp;to&nbsp;<span style="font-weight: 600;">{{new_status}}</span>.</p><p><span style="font-weight: 600;">Promo Title:</span>&nbsp;{{cr_title}}</p><p><b>Kickoff Meeting</b><span style="font-weight: 600;">&nbsp;Date:</span>&nbsp;{{kickoff_meeting_date}}</p><p>You can review it here:&nbsp;<a href="{{cr_link}}"><span style="font-weight: 600;">View Promo #{{cr_no}}</span></a></p><hr><p><i><span style="font-weight: 600;">Note:</span>&nbsp;This is an automated message sent by the IT TMS System.</i></p><p><span style="font-weight: 600;">Best regards,</span><br><span style="font-weight: 600;">TMS</span></p>',
                'available_placeholders' => '["cr_no", "group_name", "old_status", "new_status", "cr_title", "cr_link"]',
                'is_active' => 1,
                'created_at' => '2025-10-30 11:38:53',
                'updated_at' => '2025-11-20 11:39:29',
            ],
            [
                'id' => 15,
                'name' => 'Promo Status Updated (Kickoff meeting discussion)',
                'subject' => '[TMS] Promo #{{cr_no}} status has been changed',
                'body' => '<p>Dears,</p><p>The status of Promo #{{cr_no}} has been updated from&nbsp;<span style="font-weight: 600;">{{old_status}}</span>&nbsp;to&nbsp;<span style="font-weight: 600;">{{new_status}}</span>,&nbsp;and the <b>meeting</b> will begin <b>now</b>.</p><p><span style="font-weight: 600;">Promo Title:</span>&nbsp;{{cr_title}}</p><p><b>Kickoff Meeting</b><span style="font-weight: 600;">&nbsp;Date:</span>&nbsp;{{kickoff_meeting_date}}</p><p>You can review it here:&nbsp;<a href="{{cr_link}}"><span style="font-weight: 600;">View Promo #{{cr_no}}</span></a></p><hr><p><i><span style="font-weight: 600;">Note:</span>&nbsp;This is an automated message sent by the IT TMS System.</i></p><p><span style="font-weight: 600;">Best regards,</span><br><span style="font-weight: 600;">TMS</span></p>',
                'available_placeholders' => '["cr_no", "group_name", "old_status", "new_status", "cr_title", "cr_link"]',
                'is_active' => 1,
                'created_at' => '2025-10-30 11:38:53',
                'updated_at' => '2025-11-23 15:27:10',
            ],
        ];

        DB::table('notification_templates')->insert($templates);
    }

    /**
     * Seed notification rules
     */
    private function seedNotificationRules()
    {
        $rules = [
            ['id' => 46, 'name' => 'Promo CR Created - Notify CR Managers', 'event_class' => 'App\\Events\\ChangeRequestCreated', 'conditions' => '{"workflow_type": "9"}', 'template_id' => 9, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 20:42:59', 'updated_at' => '2025-10-30 20:42:59'],
            ['id' => 47, 'name' => 'Promo CR Status - CR Team FB', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "91"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 48, 'name' => 'Promo CR Status - Pending Business Feedback promo', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "176"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 49, 'name' => 'Promo CR Status - Pending Analysis', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "90"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 50, 'name' => 'Promo CR Status - Provide Estimate_CR Team', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "95"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 51, 'name' => 'Promo CR Status - Pending CD Analysis', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "138"}', 'template_id' => 12, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 52, 'name' => 'Promo CR Status - CD Analysis', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "139"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 53, 'name' => 'Promo CR Status - Pending CD FB', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "140"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 54, 'name' => 'Promo Created - Notify Requester (Promo)', 'event_class' => 'App\\Events\\ChangeRequestCreated', 'conditions' => '{"workflow_type": "9"}', 'template_id' => 7, 'is_active' => 1, 'priority' => 100, 'created_at' => '2025-10-30 20:42:59', 'updated_at' => '2025-10-30 20:42:59'],
            ['id' => 55, 'name' => 'Promo CR Status - Technical FB', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "108"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 56, 'name' => 'Promo CR Status - Provide Estimate_SA Team', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "96"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 57, 'name' => 'Promo CR Status - Pending Review CD', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "98"}', 'template_id' => 13, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 58, 'name' => 'Promo CR Status - Review CD', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "100"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 59, 'name' => 'Promo CR Status - SA FB', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "141"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 60, 'name' => 'Promo CR Status - Request Kickoff meeting', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "102"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 61, 'name' => 'Promo CR Status - Kickoff meeting', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "103"}', 'template_id' => 14, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 62, 'name' => 'Promo CR Status - Kickoff meeting discussion', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "104"}', 'template_id' => 15, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 63, 'name' => 'Promo CR Status - Update CD', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "122"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 64, 'name' => 'Promo CR Status - Pending CD Confirmation', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "142"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 65, 'name' => 'Promo CR Status - CD Confirmed', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "143"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 66, 'name' => 'Promo CR Status - Pending Technical Solution', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "144"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 67, 'name' => 'Promo CR Status - Review Tech Proposal', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "145"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 68, 'name' => 'Promo CR Status - Provide Technical FB', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "146"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 69, 'name' => 'Promo CR Status - Provide SDD Estimation', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "147"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 70, 'name' => 'Promo CR Status - Pending SDD', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "148"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 71, 'name' => 'Promo CR Status - Create Solution Design Doc', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "106"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 72, 'name' => 'Promo CR Status - Review SDD', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "109"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 73, 'name' => 'Promo CR Status - Confirmed SDD', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "149"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 74, 'name' => 'Promo CR Status - Pending SDD Modification', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "150"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 75, 'name' => 'Promo CR Status - SDD Modification', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "151"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 76, 'name' => 'Promo CR Status - Request MD\'s', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "111"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 77, 'name' => 'Promo CR Status - Set MD\'s  & Prerequisites', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "112"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 78, 'name' => 'Promo CR Status - Accumulate MS\'s', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "113"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 79, 'name' => 'Promo CR Status - Implementation plan and Priority', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "115"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 80, 'name' => 'Promo CR Status - Approved Implementation Plan', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "116"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 81, 'name' => 'Promo CR Status - Start Implementation', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "154"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 82, 'name' => 'Promo CR Status - Support Technical Issue', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "155"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 83, 'name' => 'Promo CR Status - Resume Implementation', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "156"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 84, 'name' => 'Promo CR Status - Pending Pre-requisites', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "153"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 85, 'name' => 'Promo CR Status - Deploy on UAT Environment', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "157"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 86, 'name' => 'Promo CR Status - Pending UAT (promo)', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "177"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 87, 'name' => 'Promo CR Status - Create UAT Test Cases', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "158"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 88, 'name' => 'Promo CR Status - Pending UAT Test Cases Approval', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "159"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 89, 'name' => 'Promo CR Status - UAT In Progress (promo)', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "173"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 90, 'name' => 'Promo CR Status - Pending Test Cases Rework', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "160"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 91, 'name' => 'Promo CR Status - Test Cases Rework', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "161"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 92, 'name' => 'Promo CR Status - Check Defect', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "163"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 93, 'name' => 'Promo CR Status - Defect Fixing', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "164"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 94, 'name' => 'Promo CR Status - Provide Defect Fixing Time', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "165"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 95, 'name' => 'Promo CR Status - Provide Justification', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "166"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 96, 'name' => 'Promo CR Status - Final UAT Results & FB', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "167"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 97, 'name' => 'Promo CR Status - Review UAT Results & FB', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "168"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 98, 'name' => 'Promo CR Status - Assess PMO FB', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "126"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 99, 'name' => 'Promo CR Status - Approved UAT result', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "125"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 100, 'name' => 'Promo CR Status - Pending production Deployment Promo', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "127"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 101, 'name' => 'Promo CR Status - Production Deployment In-Progress', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "170"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 102, 'name' => 'Promo CR Status - Smoke Test', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "68"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 103, 'name' => 'Promo CR Status - Assess the defects', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "132"}', 'template_id' => 10, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 104, 'name' => 'Promo CR Status - Rollback', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "134"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 105, 'name' => 'Promo CR Status - Fix defects', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "133"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 106, 'name' => 'Promo CR Status - Fix Defect on Production', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "171"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 107, 'name' => 'Promo CR Status - Cancel', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "82"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
            ['id' => 108, 'name' => 'Promo CR Status - Promo Closure', 'event_class' => 'App\\Events\\ChangeRequestStatusUpdated', 'conditions' => '{"new_status_id": "129"}', 'template_id' => 11, 'is_active' => 1, 'priority' => 90, 'created_at' => '2025-10-30 11:38:53', 'updated_at' => '2025-10-30 11:38:53'],
        ];

        DB::table('notification_rules')->insert($rules);
    }

    /**
     * Seed notification recipients
     */
    private function seedNotificationRecipients()
    {
        $recipients = [
            ['id' => 50, 'notification_rule_id' => 47, 'channel' => 'to', 'recipient_type' => 'pmo_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-17 11:20:08', 'updated_at' => '2025-11-17 11:20:13'],
            ['id' => 51, 'notification_rule_id' => 49, 'channel' => 'to', 'recipient_type' => 'cr_managers', 'recipient_identifier' => ' ', 'created_at' => '2025-11-18 09:30:36', 'updated_at' => '2025-11-18 09:30:39'],
            ['id' => 52, 'notification_rule_id' => 50, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-18 09:32:34', 'updated_at' => '2025-11-18 09:32:37'],
            ['id' => 53, 'notification_rule_id' => 51, 'channel' => 'to', 'recipient_type' => 'cr_member', 'recipient_identifier' => ' ', 'created_at' => '2025-11-18 09:36:39', 'updated_at' => '2025-11-18 09:36:42'],
            ['id' => 54, 'notification_rule_id' => 52, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-18 09:38:38', 'updated_at' => '2025-11-18 09:38:41'],
            ['id' => 55, 'notification_rule_id' => 53, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-18 10:40:00', 'updated_at' => '2025-11-18 10:40:04'],
            ['id' => 56, 'notification_rule_id' => 46, 'channel' => 'to', 'recipient_type' => 'cr_managers', 'recipient_identifier' => ' ', 'created_at' => '2025-11-18 13:19:59', 'updated_at' => '2025-11-18 13:20:02'],
            ['id' => 57, 'notification_rule_id' => 54, 'channel' => 'to', 'recipient_type' => 'cr_creator', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 09:08:16', 'updated_at' => '2025-11-20 09:08:21'],
            ['id' => 58, 'notification_rule_id' => 55, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 09:35:41', 'updated_at' => '2025-11-20 09:35:46'],
            ['id' => 59, 'notification_rule_id' => 56, 'channel' => 'to', 'recipient_type' => 'sa_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 09:40:22', 'updated_at' => '2025-11-20 09:40:26'],
            ['id' => 60, 'notification_rule_id' => 51, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 09:45:29', 'updated_at' => '2025-11-20 09:45:33'],
            ['id' => 61, 'notification_rule_id' => 57, 'channel' => 'to', 'recipient_type' => 'designer', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 09:46:52', 'updated_at' => '2025-11-20 09:46:55'],
            ['id' => 62, 'notification_rule_id' => 57, 'channel' => 'to', 'recipient_type' => 'sa_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 09:47:39', 'updated_at' => '2025-11-20 09:47:42'],
            ['id' => 63, 'notification_rule_id' => 58, 'channel' => 'to', 'recipient_type' => 'sa_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 09:49:38', 'updated_at' => '2025-11-20 09:49:41'],
            ['id' => 64, 'notification_rule_id' => 59, 'channel' => 'to', 'recipient_type' => 'sa_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 09:51:57', 'updated_at' => '2025-11-20 09:51:59'],
            ['id' => 65, 'notification_rule_id' => 60, 'channel' => 'to', 'recipient_type' => 'pmo_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 09:54:05', 'updated_at' => '2025-11-20 09:54:08'],
            ['id' => 66, 'notification_rule_id' => 61, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 11:29:53', 'updated_at' => '2025-11-20 11:29:56'],
            ['id' => 67, 'notification_rule_id' => 61, 'channel' => 'to', 'recipient_type' => 'sa_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 11:30:25', 'updated_at' => '2025-11-20 11:30:28'],
            ['id' => 68, 'notification_rule_id' => 61, 'channel' => 'to', 'recipient_type' => 'uat_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 11:30:47', 'updated_at' => '2025-11-20 11:30:49'],
            ['id' => 69, 'notification_rule_id' => 61, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 11:31:10', 'updated_at' => '2025-11-20 11:31:13'],
            ['id' => 70, 'notification_rule_id' => 62, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 11:32:04', 'updated_at' => '2025-11-20 11:32:06'],
            ['id' => 71, 'notification_rule_id' => 62, 'channel' => 'to', 'recipient_type' => 'sa_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 11:32:27', 'updated_at' => '2025-11-20 11:32:30'],
            ['id' => 72, 'notification_rule_id' => 62, 'channel' => 'to', 'recipient_type' => 'uat_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 11:32:52', 'updated_at' => '2025-11-20 11:32:55'],
            ['id' => 73, 'notification_rule_id' => 62, 'channel' => 'to', 'recipient_type' => 'pmo_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 11:33:16', 'updated_at' => '2025-11-20 11:33:19'],
            ['id' => 74, 'notification_rule_id' => 62, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 11:33:52', 'updated_at' => '2025-11-20 11:33:55'],
            ['id' => 75, 'notification_rule_id' => 63, 'channel' => 'to', 'recipient_type' => 'pmo_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 11:59:43', 'updated_at' => '2025-11-20 11:59:45'],
            ['id' => 76, 'notification_rule_id' => 64, 'channel' => 'to', 'recipient_type' => 'pmo_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 12:06:01', 'updated_at' => '2025-11-20 12:06:03'],
            ['id' => 77, 'notification_rule_id' => 65, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 12:07:03', 'updated_at' => '2025-11-20 12:07:06'],
            ['id' => 78, 'notification_rule_id' => 66, 'channel' => 'to', 'recipient_type' => 'sa_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 12:08:53', 'updated_at' => '2025-11-20 12:08:56'],
            ['id' => 79, 'notification_rule_id' => 67, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 12:37:23', 'updated_at' => '2025-11-20 12:37:26'],
            ['id' => 80, 'notification_rule_id' => 68, 'channel' => 'to', 'recipient_type' => 'sa_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 12:38:22', 'updated_at' => '2025-11-20 12:38:26'],
            ['id' => 81, 'notification_rule_id' => 69, 'channel' => 'to', 'recipient_type' => 'sa_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 12:39:27', 'updated_at' => '2025-11-20 12:39:30'],
            ['id' => 82, 'notification_rule_id' => 70, 'channel' => 'to', 'recipient_type' => 'designer', 'recipient_identifier' => '  ', 'created_at' => '2025-11-20 12:41:04', 'updated_at' => '2025-11-20 12:41:08'],
            ['id' => 83, 'notification_rule_id' => 70, 'channel' => 'to', 'recipient_type' => 'sa_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 12:46:10', 'updated_at' => '2025-11-20 12:46:12'],
            ['id' => 84, 'notification_rule_id' => 71, 'channel' => 'to', 'recipient_type' => 'sa_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-20 12:53:37', 'updated_at' => '2025-11-20 12:53:40'],
            ['id' => 85, 'notification_rule_id' => 72, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:32:21', 'updated_at' => '2025-11-23 10:32:24'],
            ['id' => 86, 'notification_rule_id' => 73, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:33:48', 'updated_at' => '2025-11-23 10:33:51'],
            ['id' => 87, 'notification_rule_id' => 74, 'channel' => 'to', 'recipient_type' => 'sa_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:34:44', 'updated_at' => '2025-11-23 10:34:47'],
            ['id' => 88, 'notification_rule_id' => 75, 'channel' => 'to', 'recipient_type' => 'sa_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:35:54', 'updated_at' => '2025-11-23 10:35:56'],
            ['id' => 89, 'notification_rule_id' => 76, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:37:23', 'updated_at' => '2025-11-23 10:37:26'],
            ['id' => 90, 'notification_rule_id' => 77, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:38:31', 'updated_at' => '2025-11-23 10:38:33'],
            ['id' => 91, 'notification_rule_id' => 78, 'channel' => 'to', 'recipient_type' => 'pmo_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:39:23', 'updated_at' => '2025-11-23 10:39:26'],
            ['id' => 92, 'notification_rule_id' => 79, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:40:25', 'updated_at' => '2025-11-23 10:40:28'],
            ['id' => 93, 'notification_rule_id' => 80, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:41:43', 'updated_at' => '2025-11-23 10:41:46'],
            ['id' => 94, 'notification_rule_id' => 81, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:43:08', 'updated_at' => '2025-11-23 10:43:12'],
            ['id' => 95, 'notification_rule_id' => 82, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:46:12', 'updated_at' => '2025-11-23 10:46:15'],
            ['id' => 96, 'notification_rule_id' => 83, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:47:15', 'updated_at' => '2025-11-23 10:47:18'],
            ['id' => 97, 'notification_rule_id' => 84, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:49:28', 'updated_at' => '2025-11-23 10:49:31'],
            ['id' => 98, 'notification_rule_id' => 85, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:51:36', 'updated_at' => '2025-11-23 10:51:39'],
            ['id' => 99, 'notification_rule_id' => 86, 'channel' => 'to', 'recipient_type' => 'uat_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:54:09', 'updated_at' => '2025-11-23 10:54:12'],
            ['id' => 100, 'notification_rule_id' => 87, 'channel' => 'to', 'recipient_type' => 'uat_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:55:40', 'updated_at' => '2025-11-23 10:55:43'],
            ['id' => 101, 'notification_rule_id' => 88, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:56:50', 'updated_at' => '2025-11-23 10:56:53'],
            ['id' => 102, 'notification_rule_id' => 88, 'channel' => 'to', 'recipient_type' => 'pmo_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 10:57:09', 'updated_at' => '2025-11-23 10:57:12'],
            ['id' => 103, 'notification_rule_id' => 89, 'channel' => 'to', 'recipient_type' => 'uat_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:00:09', 'updated_at' => '2025-11-23 11:00:13'],
            ['id' => 104, 'notification_rule_id' => 90, 'channel' => 'to', 'recipient_type' => 'uat_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:03:05', 'updated_at' => '2025-11-23 11:03:08'],
            ['id' => 105, 'notification_rule_id' => 91, 'channel' => 'to', 'recipient_type' => 'uat_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:03:54', 'updated_at' => '2025-11-23 11:03:56'],
            ['id' => 106, 'notification_rule_id' => 92, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:06:09', 'updated_at' => '2025-11-23 11:06:11'],
            ['id' => 107, 'notification_rule_id' => 93, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:07:15', 'updated_at' => '2025-11-23 11:07:18'],
            ['id' => 108, 'notification_rule_id' => 94, 'channel' => 'to', 'recipient_type' => 'uat_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:08:31', 'updated_at' => '2025-11-23 11:08:35'],
            ['id' => 109, 'notification_rule_id' => 95, 'channel' => 'to', 'recipient_type' => 'uat_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:09:58', 'updated_at' => '2025-11-23 11:10:02'],
            ['id' => 110, 'notification_rule_id' => 96, 'channel' => 'to', 'recipient_type' => 'pmo_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:11:13', 'updated_at' => '2025-11-23 11:11:15'],
            ['id' => 111, 'notification_rule_id' => 97, 'channel' => 'to', 'recipient_type' => 'pmo_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:13:05', 'updated_at' => '2025-11-23 11:13:09'],
            ['id' => 112, 'notification_rule_id' => 98, 'channel' => 'to', 'recipient_type' => 'uat_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:16:39', 'updated_at' => '2025-11-23 11:16:42'],
            ['id' => 113, 'notification_rule_id' => 99, 'channel' => 'to', 'recipient_type' => 'uat_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:17:51', 'updated_at' => '2025-11-23 11:17:54'],
            ['id' => 114, 'notification_rule_id' => 100, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:41:53', 'updated_at' => '2025-11-23 11:41:57'],
            ['id' => 115, 'notification_rule_id' => 101, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:44:09', 'updated_at' => '2025-11-23 11:44:13'],
            ['id' => 116, 'notification_rule_id' => 102, 'channel' => 'to', 'recipient_type' => 'uat_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:48:00', 'updated_at' => '2025-11-23 11:48:02'],
            ['id' => 117, 'notification_rule_id' => 103, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:49:48', 'updated_at' => '2025-11-23 11:49:51'],
            ['id' => 118, 'notification_rule_id' => 104, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 11:53:45', 'updated_at' => '2025-11-23 11:53:48'],
            ['id' => 119, 'notification_rule_id' => 105, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 12:19:31', 'updated_at' => '2025-11-23 12:19:35'],
            ['id' => 120, 'notification_rule_id' => 106, 'channel' => 'to', 'recipient_type' => 'tech_teams', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 12:21:18', 'updated_at' => '2025-11-23 12:21:20'],
            ['id' => 121, 'notification_rule_id' => 107, 'channel' => 'to', 'recipient_type' => 'pmo_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 12:27:24', 'updated_at' => '2025-11-23 12:27:28'],
            ['id' => 122, 'notification_rule_id' => 107, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 12:27:44', 'updated_at' => '2025-11-23 12:27:48'],
            ['id' => 123, 'notification_rule_id' => 108, 'channel' => 'to', 'recipient_type' => 'uat_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 12:28:20', 'updated_at' => '2025-11-23 12:28:24'],
            ['id' => 124, 'notification_rule_id' => 108, 'channel' => 'to', 'recipient_type' => 'pmo_team', 'recipient_identifier' => '  ', 'created_at' => '2025-11-23 12:28:43', 'updated_at' => '2025-11-23 12:28:47'],
            ['id' => 125, 'notification_rule_id' => 108, 'channel' => 'to', 'recipient_type' => 'cr_team', 'recipient_identifier' => ' ', 'created_at' => '2025-11-23 12:29:18', 'updated_at' => '2025-11-23 12:29:22'],
            ['id' => 126, 'notification_rule_id' => 48, 'channel' => 'to', 'recipient_type' => 'pmo_team', 'recipient_identifier' => ' ', 'created_at' => '2025-12-14 11:14:26', 'updated_at' => '2025-12-14 11:14:29'],
        ];

        DB::table('notification_recipients')->insert($recipients);
    }
}
