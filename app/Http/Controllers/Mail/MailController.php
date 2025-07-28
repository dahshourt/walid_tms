<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\MailTemplate;
use App\Models\Group;
use App\Models\change_request;


class MailController extends Controller
{


    public function index(){
        $templates = MailTemplate::get();
        $users = User::get();
        $groups = Group::get();
        return view("manual_email.create" , compact('users' , 'templates' , 'groups'));
    } //end method


    public function sendMailByTemplate($templateName, $to, $cc = [])
    {
        
        $template = MailTemplate::where('name', $templateName)->first();

        if (!$template) {
            return response()->json(['error' => 'Template not found.'], 404);
        }

        $templateContent = [
            'subject' => $template->subject,
            'body' => $template->body,
        ];

        // Send mail
        Mail::to($to)
            ->cc($cc)
            ->send(new TestMail($templateContent));

        return response()->json(['message' => 'Mail sent successfully']);
    } // end method

    public function notifyRequesterCrCreated($requester_email , $cr,$cr_no){
        $email_parts = explode('.', explode('@', $requester_email)[0]);
        $first_name = ucfirst($email_parts[0]); 

        $cr_link = route('show.cr', $cr);

        $templateContent = [
            'subject' => "CR #$cr_no has been created",
            'body' => "Dear $first_name, <br><br>"
            ."CR #$cr_no has been created successfully."
            ."<br><br>"
            ."You can review it here: <a href='$cr_link'>CR: #$cr_no</a>"
            ."<br><br>"
            ."Thank you",
        ];

        try {
            // Send the email
            Mail::to($requester_email)->send(new TestMail($templateContent));
    
            return response()->json(['success' => 'Email sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send email.', 'details' => $e->getMessage()], 500);
        }



    } // end method
     

    public function send_mail_to_cap_users($users_mail,$cr_id, $cr_no)
    {
        
        foreach ($users_mail as $key => $user) {
            $cr_link = route('edit_cab.cr', $cr_id);
             $templateContent = [
                'subject' => "CR #$cr_no Is Pending Cap",
                'body' => "Dear $user, <br><br>"
                ."CR #$cr_no has been transfered To Pending Cap Status Waiting Your Approval Or Rejection following Link."
                ."<br><br>"
                ."You can review it here: <a href='$cr_link'>CR: #$cr_no</a>"
                ."<br><br>"
                ."TMS Automation <br><br> Thank you",
            ];

            try {
                // Send the email
                Mail::to($user)->send(new TestMail($templateContent));
        
                //return response()->json(['success' => 'Email sent successfully.']);
            } catch (\Exception $e) {

                //return response()->json(['error' => 'Failed to send email.', 'details' => $e->getMessage()], 500);
            }
        }
        return true;
    }

    public function notifyDivisionManager($division_manager_email , $requester_email, $cr , $title, $description , $requester_name,$cr_no){
        
        $template = MailTemplate::where('name' ,'Notify Division Manager')->first();
        if (!$template) {
            return response()->json(['error' => 'Template not found.'], 404);
        }

        $email_parts = explode('.', explode('@', $division_manager_email)[0]);
        $first_name = ucfirst($email_parts[0]); 

        //$cr_link = route('edit.cr', $cr);
        $cr_link = route('edit.cr', ['id' => $cr, 'check_dm' => 1]);

        /*
        $cr_model = Change_request::find($cr);
        $token = md5($cr_model->id . $cr_model->created_at . env('APP_KEY')); 
        $approveLink = route('cr.division_manager.action', [
            'crId' => $cr,
            'action' => 'approve',
            'token' => $token
        ]);
        $rejectLink = route('cr.division_manager.action', [
            'crId' => $cr,
            'action' => 'reject',
            'token' => $token
        ]); 
        . "<div style='margin: 25px 0;'>"
        . "<a href='$approveLink' style='background-color: #4CAF50; color: white; padding: 10px 20px; margin-right: 10px; text-decoration: none; border-radius: 4px;'>Approve</a>"
        . " <a href='$rejectLink' style='background-color: #f44336; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Reject</a>"
        . "</div>"
        */

        $templateContent = [
            'subject' => "CR #$cr_no - Awaiting Your Approval",
            'body' => "Dear $first_name,<br><br>"
        
                . "A Change Request (CR) with ID <strong>#<a href='$cr_link'>$cr_no</a></strong> has been created and is currently "
                . "<strong>awaiting your approval or rejection</strong> as the requester's Division Manager.<br><br>"
        
                . "<ul>"
                . "<li><strong>CR Subject:</strong> $title</li>"
                . "<li><strong>CR Description:</strong> $description</li>"
                . "<li><strong>Requester:</strong> $requester_name</li>"
                . "<li><strong>Reference:</strong> CR ID #$cr_no</li>"
                . "</ul><br>"
        
                . "Please respond <strong>All</strong> to this mail with <strong>approved</strong> or <strong>rejected</strong>.<br><br>"
        
                . "Thank you in advance for your prompt action.<br><br>"
        
                . "<strong>Note:</strong> This is an automated message sent by the <strong>IT TMS System</strong>.<br>"
                . "<strong>Best regards,</strong><br>"
                . "<strong>TMS</strong>"
        ];
        

        try {
            //Send the email <a href='$cr_link'>$cr</a>
            //Mail::to($division_manager_email)->cc(['anan.latif@te.eg', 'reem.mahrous@te.eg', 'adel.atef@te.eg', 'yousry.mostafa@te.eg', 'it.qa@te.eg'])->send(new TestMail($templateContent));
            Mail::to($division_manager_email)->send(new TestMail($templateContent));
    
            return response()->json(['success' => 'Email sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send email.', 'details' => $e->getMessage()], 500);
        }
    }


    //Mail to Anan

    public function notifyCrManager($cr){
        $cr_manager_email = config('constants.mails.cr_manager');
        $email_parts = explode('.', explode('@', $cr_manager_email)[0]);
        $first_name = ucfirst($email_parts[0]); 

        $cr_link = route('show.cr', $cr);

        $templateContent = [
            'subject' => "CR #$cr status has been changed",
            'body' => "Dear $first_name, <br><br>"
            ."CR #$cr status has been changed from Review CD_CR To Promo Nature Validation."
            ."<br><br>"
            ."You can review it here: <a href='$cr_link'>CR: #$cr</a>"
            ."<br><br>"
            ."Thank you",
        ];

        try {
            // Send the email
            Mail::to($cr_manager_email)->send(new TestMail($templateContent));
    
            return response()->json(['success' => 'Email sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send email.', 'details' => $e->getMessage()], 500);
        }



    } // end method

    // to send the mail
    //sendMailByTemplate('Template Name', 'recipient@example.com', ['cc@example.com']);
}
