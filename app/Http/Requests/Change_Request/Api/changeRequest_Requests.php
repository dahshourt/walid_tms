<?php

namespace App\Http\Requests\Change_Request\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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

        return [
            'title' => ['required','string'],//requester_department
            'requester_department' => ['sometimes','nullable','string'],
            'description' => ['required','string'],
            //'active' => ['required'],
            'testable' => ['sometimes'],
            'depend_cr_id' => ['sometimes','nullable','integer', 'exists:change_request,id'],
            'category_id' => ['sometimes','nullable','integer', 'exists:categories,id'],
            'priority_id' => ['sometimes','nullable','integer', 'exists:priorities,id'],
            'unit_id' => ['sometimes','nullable','integer', 'exists:units,id'],
            'department_id' => ['sometimes','nullable','integer', 'exists:departments,id'],
            'application_id' => ['sometimes','nullable','integer', 'exists:applications,id'],
            'helpdesk_id' => ['sometimes','nullable','integer'],
            'depend_cr_id' => ['sometimes','nullable','integer'],
            'requester_name' => ['sometimes','nullable','string'],
            'requester_email' => ['sometimes','nullable','string'],
            'requester_unit' => ['sometimes','nullable','string'],
            'requester_division_manager' => ['sometimes','nullable','string'],
          
            'application_name' => ['sometimes','nullable','string'],
            'creator_mobile_number' => ['required','regex:/^01[0-9]{9}$/'],

        ]; 

    }

    public function updateRules()
    {
        return [

           /* 'title' => ['sometimes','string'],
            'description' => ['sometimes','string'],
            'active' => ['sometimes'],
            'testable' => ['sometimes'],
            'depend_cr_id' => ['sometimes','nullable','integer', 'exists:change_request,id'],
            'category_id' => ['sometimes','nullable','integer', 'exists:categories,id'],
            'priority_id' => ['sometimes','nullable','integer', 'exists:priorities,id'],
            'unit_id' => ['sometimes','nullable','integer', 'exists:units,id'],
            'department_id' => ['sometimes','nullable','integer', 'exists:departments,id'],
            'application_id' => ['sometimes','nullable','integer', 'exists:applications,id'],
            'helpdesk_id' => ['sometimes','nullable','integer'],
            'depend_cr_id' => ['sometimes','nullable','integer'],
            'requester_name' => ['sometimes','nullable','string'],
            'requester_email' => ['sometimes','nullable','string'],
            'requester_unit' => ['sometimes','nullable','string'],
            'requester_division_manager' => ['sometimes','nullable','string'],
            'requester_department' => ['sometimes','nullable','string'],
            'application_name' => ['sometimes','nullable','string'],*/
            // 'old_status_id ' => ['required',"integer"],
            // 'new_status_id ' => ['required',"integer"]

        ];
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
    return [
        'title.required' => 'The CR Subject field is required.',
        'title.string' => 'The CR Subject must be a valid string.',
        'creator_mobile_number.regex' => 'The mobile number is invalid',

    ];
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
