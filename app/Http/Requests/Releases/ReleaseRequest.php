<?php

namespace App\Http\Requests\Releases;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReleaseRequest extends FormRequest
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
            'name' => ['required', 'string', 'unique:releases,name'],
            'go_live_planned_date' => [ 'sometimes', 'nullable','date'],
            'planned_start_iot_date' => ['sometimes', 'nullable','date'],
            'planned_end_iot_date' => [ 'sometimes', 'nullable','date', 'after_or_equal:planned_start_iot_date'],
            'planned_start_e2e_date' => [ 'sometimes', 'nullable','date'],
            'planned_end_e2e_date' => ['sometimes', 'nullable','date', 'after_or_equal:planned_start_e2e_date'],
            'planned_start_uat_date' => ['sometimes', 'nullable','date'],
            'planned_end_uat_date' => [ 'sometimes', 'nullable','date', 'after_or_equal:planned_start_uat_date'],
            'planned_start_smoke_test_date' => [ 'sometimes', 'nullable','date'],
            'planned_end_smoke_test_date' => [ 'sometimes', 'nullable','date', 'after_or_equal:planned_start_smoke_test_date'],
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
            'name' => ['required', 'string', 'unique:releases,name,' . $this->release],
            'go_live_planned_date' => ['sometimes', 'nullable', 'date'],
            'planned_start_iot_date' => [ 'sometimes', 'nullable','date'],
            'planned_end_iot_date' => [ 'sometimes', 'nullable','date', 'after_or_equal:planned_start_iot_date'],
            'planned_start_e2e_date' => ['sometimes', 'nullable', 'date'],
            'planned_end_e2e_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:planned_start_e2e_date'],
            'planned_start_uat_date' => ['sometimes', 'nullable', 'date'],
            'planned_end_uat_date' => [ 'sometimes', 'nullable','date', 'after_or_equal:planned_start_uat_date'],
            'planned_start_smoke_test_date' => [ 'sometimes', 'nullable','date'],
            'planned_end_smoke_test_date' => [ 'sometimes', 'nullable','date', 'after_or_equal:planned_start_smoke_test_date'],
        ];
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
