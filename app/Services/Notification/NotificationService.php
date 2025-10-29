<?php

namespace App\Services\Notification;

use App\Models\NotificationRule;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicNotification;

class NotificationService
{
    public function handleEvent($event)
    {
        $eventClass = get_class($event);
        
        // Get all active rules for this event
        $rules = NotificationRule::with(['template', 'recipients'])
            ->where('event_class', $eventClass)
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($rules as $rule) {
            // Check if conditions match
            if ($this->evaluateConditions($rule, $event)) {
                $this->processNotification($rule, $event);
            }
        }
    }

    protected function evaluateConditions($rule, $event)
    {
        if (empty($rule->conditions)) {
            return true; // No conditions = always execute
        }

        $conditions = $rule->conditions;
        
        // Check workflow_type condition (when workflow promo)
        if (isset($conditions['workflow_type'])) {
            if ($event->changeRequest->workflow_type_id != $conditions['workflow_type']) {
                return false;
            }
        }
        
        // Check workflow_type_not condition (when workflow should not promo)
        if (isset($conditions['workflow_type_not'])) {
            if ($event->changeRequest->workflow_type_id == $conditions['workflow_type_not']) {
                return false;
            }
        }
    
        if (isset($conditions['from_status_id'])) {
            if (isset($event->oldStatusId) && $event->oldStatusId != $conditions['from_status_id']) {
                return false;
            }
        }
        
        if (isset($conditions['to_status_id'])) {
            if (isset($event->newStatusId) && $event->newStatusId != $conditions['to_status_id']) {
                return false;
            }
        }
        
        return true;
    }

    protected function processNotification($rule, $event)
    {
        // Resolve recipients (get the recipients that will receive the notification)
        $recipients = $this->resolveRecipients($rule, $event);
        
        if (empty($recipients['to'])) {
            return; // No recipients no notification will be sent
        }

        // get the template the will be sent
        $rendered = $this->renderTemplate($rule->template, $event, $rule);

        // create log entry
        $log = $this->createLog($rule, $event, $recipients, $rendered);

        // Send email (queued)
        try {
            Mail::to($recipients['to'])
                ->cc($recipients['cc'] ?? [])
                ->bcc($recipients['bcc'] ?? [])
                ->queue(new DynamicNotification($rendered['subject'], $rendered['body']));

            $log->update(['status' => 'queued']);
        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }

    protected function resolveRecipients($rule, $event)
    {
        $resolved = ['to' => [], 'cc' => [], 'bcc' => []];

        foreach ($rule->recipients as $recipient) {
            $emails = $this->getRecipientEmails($recipient, $event);
            $resolved[$recipient->channel] = array_merge(
                $resolved[$recipient->channel], 
                $emails
            );
        }

        return $resolved;
    }
    
    // the recipients emails
    protected function getRecipientEmails($recipient, $event)
    {
        switch ($recipient->recipient_type) {
            case 'cr_creator':
                return [$event->creator->email ?? $event->changeRequest->requester_email];
            
            case 'division_manager':
                // Get division manager from statusData or from CR model
                if (isset($event->statusData['division_manager'])) {
                    return [$event->statusData['division_manager']];
                }
                // get it from the model
                if (isset($event->changeRequest->division_manager_id)) {
                    $dm = $event->changeRequest->division_manager;
                    return $dm ? [$dm->email] : [];
                }
                return [];
            // in case sent to specific email
            case 'static_email':
                return [$recipient->recipient_identifier];
            
            case 'user':
                $user = \App\Models\User::find($recipient->recipient_identifier);
                return $user ? [$user->email] : [];
            
            case 'group':
                $group = \App\Models\Group::find($recipient->recipient_identifier);
                return $group ? [$group->head_group_email] : [];
            
            case 'developer':
                if (isset($event->changeRequest->developer_id)) {
                    $dev = $event->changeRequest->developer;
                    return $dev ? [$dev->email] : [];
                }
                return [];
            
            case 'tester':
                if (isset($event->changeRequest->tester_id)) {
                    $tester = $event->changeRequest->tester;
                    return $tester ? [$tester->email] : [];
                }
                return [];
            
            default:
                return [];
        }
    }

    protected function renderTemplate($template, $event, $rule)
    {
        $placeholders = $this->extractPlaceholders($event, $rule);
        
        $subject = $this->replacePlaceholders($template->subject, $placeholders);
        $body = $this->replacePlaceholders($template->body, $placeholders);

        return compact('subject', 'body');
    }

    protected function extractPlaceholders($event, $rule)
    {
        // Extract data from event
        $cr = $event->changeRequest;
        $statusData = $event->statusData ?? [];
        
        // Get creator/requester name
        $creatorName = 'User';
        if ($event->creator && isset($event->creator->user_name)) {
            $creatorName = $event->creator->user_name;
        } elseif (isset($statusData['requester_name'])) {
            $creatorName = $statusData['requester_name'];
        } elseif (isset($cr->requester_name)) {
            $creatorName = $cr->requester_name;
        }
        
        // Extract first name from email if available
        $firstName = $creatorName;
        if ($event->creator && isset($event->creator->email)) {
            $email_parts = explode('.', explode('@', $event->creator->email)[0]);
            $firstName = ucfirst($email_parts[0]);
        } elseif (isset($statusData['requester_email'])) {
            $email_parts = explode('.', explode('@', $statusData['requester_email'])[0]);
            $firstName = ucfirst($email_parts[0]);
        }
        
        // Get division manager name if available
        $divisionManagerName = '';
        $divisionManagerEmail = '';
        if (isset($statusData['division_manager'])) {
            $divisionManagerEmail = $statusData['division_manager'];
            $email_parts = explode('.', explode('@', $divisionManagerEmail)[0]);
            $divisionManagerName = ucfirst($email_parts[0]);
        }
        
        // CR link based on workflow type
        $crLink = route('show.cr', $cr->id);
        // if the rule is notify devision manager 
        if ($rule->name == config('constants.rules.notify_division_manager_default')) {
            $crLink = route('edit.cr', ['id' => $cr->id, 'check_dm' => 1]);
        }
        
        // Get QC email (RPA) and ticketing dev from config
        $qcEmail = config('constants.mails.qc_mail', '');
        $ticketingDev = config('constants.mails.ticketing_dev_mail', '');
        $replyToEmail = config('mail.from.address', '');
        $subject = "Re: CR #{$cr->cr_no} - Awaiting Your Approval";
        
        $approveLink = "mailto:{$qcEmail}?subject=" . rawurlencode($subject) . "&cc={$replyToEmail};{$ticketingDev}&body=approved";
        $rejectLink = "mailto:{$qcEmail}?subject=" . rawurlencode($subject) . "&cc={$replyToEmail};{$ticketingDev}&body=rejected";
        
        return [
            'cr_no' => $cr->cr_no,
            'cr_id' => $cr->id,
            'cr_title' => $statusData['title'] ?? $cr->title ?? '',
            'cr_description' => $statusData['description'] ?? $cr->description ?? '',
            'creator_name' => $creatorName,
            'requester_name' => $creatorName,
            'first_name' => $firstName,
            'division_manager_name' => $divisionManagerName,
            'current_status' => $cr->currentStatusRel->status->status_name ?? 'N/A',
            'cr_link' => $crLink,
            'approve_link' => $approveLink,
            'reject_link' => $rejectLink,
            'workflow_type_id' => $cr->workflow_type_id ?? '',
            
        ];
    }

    protected function replacePlaceholders($text, $placeholders)
    {
        foreach ($placeholders as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }
        return $text;
    }

    protected function createLog($rule, $event, $recipients, $rendered)
    {
        return NotificationLog::create([
            'notification_rule_id' => $rule->id,
            'template_id' => $rule->template_id,
            'event_class' => get_class($event),
            'event_data' => [
                'cr_id' => $event->changeRequest->id ?? null,
            ],
            'subject' => $rendered['subject'],
            'body' => $rendered['body'],
            'recipients_to' => $recipients['to'],
            'recipients_cc' => $recipients['cc'] ?? [],
            'recipients_bcc' => $recipients['bcc'] ?? [],
            'status' => 'pending',
            'related_model_type' => get_class($event->changeRequest ?? null),
            'related_model_id' => $event->changeRequest->id ?? null,
        ]);
    }
}