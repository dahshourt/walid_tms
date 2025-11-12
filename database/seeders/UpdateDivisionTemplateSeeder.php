<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateDivisionTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notification_templates')
            ->where('id', 2)
            ->update([
                'name' => 'CR Created - Notify Division Manager (Regular CR)',
                'subject' => 'CR #{{cr_no}} - Awaiting Your Approval',
                'body' => '<p>Dear {{division_manager_name}},</p>
                <p>Change Request (CR) #{{cr_no}} has been submitted and is waiting for your action.</p>
                <p>Please use one of the buttons below to respond:</p>

                <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin: 10px 0;">
                <tr>
                    <td style="padding: 10px;">
                    <a href="{{approve_link}}" style="background-color: #28a745; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">✓ Approve</a>
                    </td>
                    <td style="padding: 10px;">
                    <a href="{{reject_link}}" style="background-color: #dc3545; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">✗ Reject</a>
                    </td>
                    <td style="padding: 10px;">
                    <a href="{{system_link}}" style="background-color: #007bff; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">View in TMS</a>
                    </td>
                </tr>
                </table>

                <p><i>To review this request directly in the TMS system, click "View in TMS" and select "CRs Waiting Approval".</i></p>

                <hr>
                <blockquote><p><b><font style="background-color: rgb(255, 0, 0);" color="#ffffff">Disclaimer</font>: </b>Kindly <b>avoid</b> using "<b>Reply</b>," "<b>Reply All</b>," or <b>forwarding</b> this email. Your action <b>will be lost</b> and not recorded if you do not use the buttons above.</p></blockquote>
                <hr>
                <p><b>Change Request Information:</b></p>
                <ul>
                <li><p><b>CR Subject:</b>&nbsp;{{cr_title}}</p></li>
                <li><p><b>CR Description:</b>&nbsp;{{cr_description}}</p></li>
                <li><p><b>Requester:</b> {{requester_name}}</p></li>
                </ul>
                <p>Thank you in advance for your prompt action.</p>
                <p>Best regards, TMS</p>
                <p><i>Note: This is an automated message sent by the IT TMS System.</i></p>',
                'available_placeholders' => json_encode([
                    'cr_no',
                    'division_manager_name',
                    'cr_title',
                    'cr_description',
                    'requester_name',
                    'cr_link',
                    'approve_link',
                    'reject_link',
                    'system_link'
                ]),
                'is_active' => 1,
                'updated_at' => now()
            ]);

        $this->command->info('Division manager notification template updated successfully.');
    }
}
