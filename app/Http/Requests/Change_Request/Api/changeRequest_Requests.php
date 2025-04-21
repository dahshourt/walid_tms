<?php

namespace App\Http\Requests\Change_Request\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Repository\CustomField\CustomFieldGroupTypeRepository;

class changeRequest_Requests extends FormRequest
{

    /**
     * Determine if the supervisor is authorized to make this request.
     *
     * @return bool
     */
    // public function authorize()
    // {
    //    return true;
    // }



    public function rules()
    {
        if ($this->isMethod('POST')) {
            return $this->createRules();
        } else {
            return $this->updateRules();
        }
    }


    /**
     * Get the create validation rules that apply to the request.
     *
     * @return array
     */
    public function createRules()
    {
        $formFields=new CustomFieldGroupTypeRepository();
        $formFields = $formFields->CustomFieldsByWorkFlowType($this->workflow_type_id, 1);
        $rules = [];
        foreach ($formFields as $field) {
            if($field->validation_type_id == 1){
                if($field->CustomField->name == "division_manager")
                {
                    $rules[$field->CustomField->name] = "required|email";
                }
                elseif($field->CustomField->name == "creator_mobile_number")
                {
                    $rules[$field->CustomField->name] = "required|regex:/^01[0-9]{9}$/";
                }
                else
                {
                    $rules[$field->CustomField->name] = "required";
                }
                
            }
        }
        return $rules;
       

    }

    public function updateRules()
    {
        $formFields=new CustomFieldGroupTypeRepository();
        $formFields = $formFields->CustomFieldsByWorkFlowTypeAndStatus($this->workflow_type_id, 2,$this->old_status_id);
        $rules = [];
        foreach ($formFields as $field) {
            if($field->validation_type_id == 1){
                if($field->CustomField->name == "division_manager")
                {
                    $rules[$field->CustomField->name] = "required|email";
                }
                elseif($field->CustomField->name == "creator_mobile_number")
                {
                    $rules[$field->CustomField->name] = "required|regex:/^01[0-9]{9}$/";
                }
                else
                {
                    $rules[$field->CustomField->name] = "required";
                }
                
            }
        }
        return $rules;
    }


    // protected function failedValidation(Validator $validator) {
    //     throw new HttpResponseException(response()->json([
    //         'message' => $validator->messages()
    //       ], 422));
    // }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    // public function attributes()
    // {

    // }

    public function messages()
    {
        $messages = [];
        if ($this->isMethod('POST')) {
            $formFields=new CustomFieldGroupTypeRepository();
            $formFields = $formFields->CustomFieldsByWorkFlowType($this->workflow_type_id, 1);
            
            foreach ($formFields as $field) {
                foreach ($formFields as $field) {
                    if($field->validation_type_id == 1){
                        if($field->CustomField->name == "division_manager")
                        {
                            $field_name = str_replace('_', ' ', $field->CustomField->label);
                            $messages["{$field->CustomField->name}.required"] = "{$field_name} is required";
                            $messages["{$field->CustomField->name}.email"] = "{$field_name} must be a valid email";
                        }
                        elseif($field->CustomField->name == "creator_mobile_number")
                        {
                            $field_name = str_replace('_', ' ', $field->CustomField->label);
                            $messages["{$field->CustomField->name}.required"] = "{$field_name} is required";
                            $messages["{$field->CustomField->name}.regex"] = "{$field_name} must be 11 digit with start of 01";
                        }
                        else
                        {
                            $field_name = str_replace('_', ' ', $field->CustomField->label);
                            $messages["{$field->CustomField->name}.required"] = "{$field_name} is required";
                        }
                        
                    }
                    //
                }
            }
        }
        else
        {

            $formFields=new CustomFieldGroupTypeRepository();
            $formFields = $formFields->CustomFieldsByWorkFlowTypeAndStatus($this->workflow_type_id, 2,$this->old_status_id);
            
            foreach ($formFields as $field) {
                foreach ($formFields as $field) {
                    if($field->validation_type_id == 1){
                        if($field->CustomField->name == "division_manager")
                        {
                            $field_name = str_replace('_', ' ', $field->CustomField->label);
                            $messages["{$field->CustomField->name}.required"] = "{$field_name} is required";
                            $messages["{$field->CustomField->name}.email"] = "{$field_name} must be a valid email";
                        }
                        elseif($field->CustomField->name == "creator_mobile_number")
                        {
                            $field_name = str_replace('_', ' ', $field->CustomField->label);
                            $messages["{$field->CustomField->name}.required"] = "{$field_name} is required";
                            $messages["{$field->CustomField->name}.regex"] = "{$field_name} must be 11 digit with start of 01";
                        }
                        else
                        {
                            $field_name = str_replace('_', ' ', $field->CustomField->label);
                            $messages["{$field->CustomField->name}.required"] = "{$field_name} is required";
                        }
                        
                    }
                    //
                }
            }
        }
        return $messages;
       
    }

public function attributes()
{
    return [
        'title' => 'CR Subject',
    ];
}
protected function prepareForValidation()
    {
        // Set 'active' to 1 if not present in the request
        $this->merge([
            'active' => $this->has('active') ? '1' : '0',//testable
            'testable' => $this->has('testable') ? '1' : '0',//need_ux_ui
            'need_ux_ui' => $this->has('need_ux_ui') ? 1 : 0,

        ]);
    }
}
