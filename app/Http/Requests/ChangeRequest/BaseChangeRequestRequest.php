<?php

namespace App\Http\Requests\ChangeRequest;

use App\Http\Repository\ChangeRequest\ChangeRequestRepository;
use App\Http\Repository\CustomField\CustomFieldGroupTypeRepository;
use App\Rules\CompareOldValue;
use App\Rules\DivisionManagerExists;
use App\Rules\ValidateStatus;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseChangeRequestRequest extends FormRequest
{
    protected const ALLOWED_MIMES = [
        'doc', 'docx', 'xls', 'xlsx', 'pdf', 'zip', 'rar',
        'jpeg', 'jpg', 'png', 'gif', 'msg'
    ];

    protected const ALLOWED_MIME_TYPES = [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/pdf',
        'application/zip',
        'application/x-rar-compressed',
        'application/x-rar',
        'application/vnd.rar',
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/vnd.ms-outlook',
        'text/html'
    ];

    protected const MAX_FILE_SIZE = 51200; // 50MB in KB

    public function authorize(): bool
    {
        return true;
    }

    protected function getAttachmentRules(): array
    {
        $rules = [];
        $attachmentTypes = ['technical_attachments', 'business_attachments'];

        foreach ($attachmentTypes as $type) {
            $rules["{$type}.*"] = [
                'nullable',
                'file',
                'mimes:' . implode(',', self::ALLOWED_MIMES),
                'mimetypes:' . implode(',', self::ALLOWED_MIME_TYPES),
                'max:' . self::MAX_FILE_SIZE
            ];
        }

        return $rules;
    }

    protected function getAttachmentMessages(): array
    {
        $messages = [];
        $attachmentTypes = ['technical_attachments', 'business_attachments'];

        foreach ($attachmentTypes as $type) {
            $messages["{$type}.*.mimes"] = 'Only ' . implode(',', self::ALLOWED_MIMES) . ' files are allowed';
            $messages["{$type}.*.mimetypes"] = 'Only ' . implode(',', self::ALLOWED_MIMES) . ' files are allowed';
            $messages["{$type}.*.max"] = 'Maximum file size is 50MB';
        }

        return $messages;
    }

    protected function getDynamicRules(int $formType, ?int $statusId = null): array
    {
        $repo = new CustomFieldGroupTypeRepository();
        
        if ($formType === 1) { // Create
            $formFields = $repo->CustomFieldsByWorkFlowType($this->workflow_type_id, 1);
        } else { // Edit
            $formFields = $repo->CustomFieldsByWorkFlowTypeAndStatus($this->workflow_type_id, 2, $statusId);
        }

        $rules = [];
        foreach ($formFields as $field) {
            // Skip if not enabled and not in special handling
            if ($formType === 2 && $field->enable == 0) {
                if ($field->CustomField->name != 'need_design' && $field->CustomField->name != 'technical_teams') {
                    $oldValue = $this->cr->{$field->CustomField->name} ?? null;
                    $rules[$field->CustomField->name] = [new CompareOldValue($oldValue)];
                }
                continue;
            }

            if ($field->validation_type_id == 1) { // Required
                if ($field->CustomField->name == 'division_manager') {
                    $rules[$field->CustomField->name] = ['required', 'email', new DivisionManagerExists()];
                } elseif ($field->CustomField->name == 'creator_mobile_number') {
                    $rules[$field->CustomField->name] = 'required|regex:/^01[0-9]{9}$/';
                } elseif ($field->CustomField->name == 'new_status_id' && $formType === 2) {
                     $allowedStatusIds = $this->cr->set_status->pluck('id')->toArray();
                     $rules[$field->CustomField->name] = [new ValidateStatus($allowedStatusIds)];
                } else {
                    $rules[$field->CustomField->name] = 'required';
                }
            }
        }

        return $rules;
    }

    protected function getDynamicMessages(int $formType, ?int $statusId = null): array
    {
        $repo = new CustomFieldGroupTypeRepository();
        
        if ($formType === 1) {
            $formFields = $repo->CustomFieldsByWorkFlowType($this->workflow_type_id, 1);
        } else {
            $formFields = $repo->CustomFieldsByWorkFlowTypeAndStatus($this->workflow_type_id, 2, $statusId);
        }

        $messages = [];
        foreach ($formFields as $field) {
            if ($field->validation_type_id == 1) {
                $fieldName = str_replace('_', ' ', $field->CustomField->label);
                
                if ($field->CustomField->name == 'division_manager') {
                    $messages["{$field->CustomField->name}.required"] = "{$fieldName} is required";
                    $messages["{$field->CustomField->name}.email"] = "{$fieldName} must be a valid email";
                } elseif ($field->CustomField->name == 'creator_mobile_number') {
                    $messages["{$field->CustomField->name}.required"] = "{$fieldName} is required";
                    $messages["{$field->CustomField->name}.regex"] = "{$fieldName} must be 11 digit with start of 01";
                } else {
                    $messages["{$field->CustomField->name}.required"] = "{$fieldName} is required";
                }
            }
        }

        return $messages;
    }
}
