<?php

namespace App\Http\Repository\NotificationRules;

use App\Contracts\NotificationRules\NotificationRulesRepositoryInterface;
use App\Models\NotificationRule;
use App\Models\NotificationRecipient;
use Illuminate\Support\Facades\DB;

class NotificationRulesRepository implements NotificationRulesRepositoryInterface
{
    
    // Get all notification rules with pagination.
    public function getAll()
    {
        return NotificationRule::with(['template', 'recipients'])
            ->paginate(10);
    }

    public function find($id)
    {
        return NotificationRule::find($id);
    }

    // Get notification rule with recipients eager loaded.
    public function getWithRecipients($id)
    {
        return NotificationRule::with(['template', 'recipients'])->find($id);
    }

    // Create a new notification rule with recipients.
    public function create($data)
    {
        return DB::transaction(function () use ($data) {
            // Build conditions JSON
            $conditions = null;
            if (!empty($data['condition_type']) && !empty($data['condition_value'])) {
                $conditions = [$data['condition_type'] => $data['condition_value']];
            }

            // Create the rule
            $rule = NotificationRule::create([
                'name' => $data['name'],
                'event_class' => $data['event_class'],
                'template_id' => $data['template_id'],
                'conditions' => $conditions,
                'priority' => $data['priority'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Create recipients
            if (!empty($data['recipients']) && is_array($data['recipients'])) {
                $this->syncRecipients($rule, $data['recipients']);
            }

            return $rule;
        });
    }

    // Update a notification rule and its recipients.
    public function update($data, $id)
    {
        return DB::transaction(function () use ($data, $id) {
            $rule = NotificationRule::findOrFail($id);

            // Build conditions JSON
            $conditions = null;
            if (!empty($data['condition_type']) && !empty($data['condition_value'])) {
                $conditions = [$data['condition_type'] => $data['condition_value']];
            }

            // Update the rule
            $rule->update([
                'name' => $data['name'],
                'event_class' => $data['event_class'],
                'template_id' => $data['template_id'],
                'conditions' => $conditions,
                'priority' => $data['priority'] ?? 0,
                'is_active' => $data['is_active'] ?? false,
            ]);

            // Sync recipients - delete old and create new
            $rule->recipients()->delete();
            if (!empty($data['recipients']) && is_array($data['recipients'])) {
                $this->syncRecipients($rule, $data['recipients']);
            }

            return $rule->fresh(['template', 'recipients']);
        });
    }

    // Delete a notification rule.
    public function delete($id)
    {
        $rule = NotificationRule::find($id);
        if ($rule) {
            // Recipients will be cascade deleted due to foreign key constraint
            return $rule->delete();
        }
        return false;
    }

    // Sync recipients for a rule.
    protected function syncRecipients(NotificationRule $rule, array $recipients)
    {
        foreach ($recipients as $recipient) {
            // Skip empty rows
            if (empty($recipient['recipient_type'])) {
                continue;
            }

            // Get the recipient type configuration
            $recipientTypes = collect(config('notification_recipient_types', []));
            $typeConfig = $recipientTypes->firstWhere('value', $recipient['recipient_type']);

            // Only set identifier if this type needs one
            $identifier = null;
            if ($typeConfig && $typeConfig['needs_identifier']) {
                $identifier = $recipient['recipient_identifier'] ?? null;
            }

            NotificationRecipient::create([
                'notification_rule_id' => $rule->id,
                'channel' => $recipient['channel'] ?? 'to',
                'recipient_type' => $recipient['recipient_type'],
                'recipient_identifier' => $identifier,
            ]);
        }
    }

    // Get all rules as a list (no pagination).
    public function list()
    {
        return NotificationRule::with(['template', 'recipients'])
            ->get();
    }
}
