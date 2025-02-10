<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\MailTemplate;
use App\Models\Group;


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

    public function notifyRequesterCrCreated($requester_email , $cr){
        $email_parts = explode('.', explode('@', $requester_email)[0]);
        $first_name = ucfirst($email_parts[0]); 

        $cr_link = route('show.cr', $cr);

        $templateContent = [
            'subject' => "CR #$cr has been created",
            'body' => "Dear $first_name, <br><br>"
            ."CR #$cr has been created successfully."
            ."<br><br>"
            ."You can review it here: <a href='$cr_link'>CR: #$cr</a>"
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
     

    public function send_mail_to_cap_users($users_mail, $cr_no)
    {
        foreach ($users_mail as $key => $user) {
             $templateContent = [
                'subject' => "CR #$cr_no Is Pending Cap",
                'body' => "Dear $user, <br><br>"
                ."CR #$cr_no has been transfered To Pending Cap Status Waiting Your Approval Or Rejection following Link."
                ."<br><br>"
                ."You can review it here: <a href='#'>CR: #$cr_no</a>"
                ."<br><br>"
                ."TMS Automation <br><br> Thank you",
            ];

            try {
                // Send the email
                Mail::to($requester_email)->send(new TestMail($templateContent));
        
                return response()->json(['success' => 'Email sent successfully.']);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to send email.', 'details' => $e->getMessage()], 500);
            }
        }
    }

    public function notifyDivisionManager($division_manager_email , $requester_email, $cr , $title, $description , $requester_name){
        
        $template = MailTemplate::where('name' ,'Notify Division Manager')->first();
        if (!$template) {
            return response()->json(['error' => 'Template not found.'], 404);
        }

        $email_parts = explode('.', explode('@', $division_manager_email)[0]);
        $first_name = ucfirst($email_parts[0]); 

        $cr_link = route('edit.cr', $cr);

        $templateContent = [
            'subject' => $template->subject . " #$cr",
            'body' => "Dear $first_name, <br><br>"
            . $template->body 
            . "<br><br>"
            . "TMS (Ref: CR ID #<a href='$cr_link'>$cr</a>)"
            . "<br><br>"
            . "CR Subject: $title"
            . "<br>"
            . "CR Description: $description"
            . "<br><br>"
            . "Requester: $requester_name"
            . "<br>"
            . "Thank You",
        ];

        try {
            // Send the email
            Mail::to($division_manager_email)->cc($requester_email)->send(new TestMail($templateContent));
    
            return response()->json(['success' => 'Email sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send email.', 'details' => $e->getMessage()], 500);
        }
    }

    // to send the mail
    //sendMailByTemplate('Template Name', 'recipient@example.com', ['cc@example.com']);

}
