<?php

namespace App\Http\Requests\Statuses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StatusRequest extends FormRequest
{

    /**
     * Determine if the supervisor is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
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
            'status_name' => ['required','string', 'unique:statuses'],
            'stage_id' => ['required','integer'],
			'sla' => ['required','integer'],
            'active' => ['integer'],
            'set_group_id'   => ['required','array'],
            'set_group_id.*' => ['integer', 'exists:groups,id'],
            'view_group_id'   => ['sometimes','array'],
            'view_group_id.*' => ['integer', 'exists:groups,id']
            
        ];
    }

    /**
     * Get the update validation rules that apply to the request.
     *
     * @return array
     */
    public function updateRules()
    {
        return [

            'status_name' => ['required','string', 'unique:statuses,status_name,' . request()->status],
            'stage_id' => ['required','integer'],
            'sla' => ['required','integer'],
            'active' => ['required','integer'],
            'group_id'   => ['sometimes','array'],
            'group_id.*' => ['integer', 'exists:groups,id'],
            'view_group_id'   => ['sometimes','array'],
            'view_group_id.*' => ['integer', 'exists:groups,id']
        ];
    }


    protected function prepareForValidation()
    {
        // Set 'active' to 1 if not present in the request
        $this->merge([
            'active' => $this->has('active') ? $this->input('active') : '0',
        ]);
    }
    

    

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    // public function attributes()
    // {

    // }
}
