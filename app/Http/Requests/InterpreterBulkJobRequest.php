<?php

namespace App\Http\Requests;

use App\Rules\ValidAgentForJob;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InterpreterBulkJobRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->user()->hasRole('client')) {
            $this->merge([
                'client_id' => $this->user()->client->id
            ]);
        }
    }

    public function rules()
    {
        $rules = [
            'to_language_id' => ['required', 'exists:languages,id'],
            'skill_id' => ['required', 'exists:skills,id'],
            'requested_agent_id' => ['nullable', 'array'], // Ensure it's an array
        'requested_agent_id.*' => ['nullable', 'integer'], // Validate each ID
            'require_qualified' => ['required', 'integer'],
            'security_type_id' => ['required', 'integer'],
            'gender' => ['required', Rule::in(array_keys(config('enums.genders')))],
            'appointment_date' => ['required', 'array'],
            'appointment_date.*' => ['required', 'date'],
            'start_time' => ['required', 'array'],
            'start_time.*' => ['required', 'date_format:H:i:s'],
            'duration_hours' => ['required', 'array'],
            'duration_hours.*' => ['required', 'integer', 'min:0'],
            'duration_minutes' => ['required', 'array'],
            'duration_minutes.*' => ['required', 'digits_between:0,59'],
            'client_reference' => ['nullable', 'string', 'max:255'],
            'user_title' => ['nullable', Rule::in(array_keys(config('enums.titles')))],
            'user_first_name' => ['nullable', 'string', 'max:255'],
            'user_last_name' => ['nullable', 'string', 'max:255'],
            'personal_identity_number' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'address_line_1' => ['required_if:skill_id, 1, 2', 'nullable', 'max:255'],
            'address_line_2' => ['nullable', 'max:255'],
            'county' => ['nullable', 'max:255'],
            'postcode' => ['required_if:skill_id, 1, 2', 'nullable', 'max:255'],
            'contact_information' => ['nullable', 'string', 'max:255'],
            'special_requirements' => ['nullable', 'string', 'max:255'],
            'file_reference' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'client_id' => ['sometimes', 'exists:clients,id'],
            'service_user_required' => ['sometimes', 'boolean'],
            'contact_information_is_same_as_account' => ['sometimes', 'boolean'],
            'bulk_id' => ['nullable', 'max:255'],
        ];

        // Conditional validation for duration_hours based on skill_id
        if ($this->skill_id === "1") {
            $rules['duration_hours.*'] = ['required', 'integer', 'min:1'];
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [];

        // Custom messages for appointment_date and duration_hours based on skill_id
        foreach ($this->appointment_date as $key => $value) {
            if ($this->skill_id === "1") {
                $messages["duration_hours.$key.min"] = "The duration of a face-to-face meeting at index $key must be at least 1 hour.";
            } else {
                $messages["duration_hours.$key.min"] = "The duration of a meeting at index $key must be more than 0 hours.";
            }
        }

        $messages['address_line_1.required_if'] = 'The address line 1 field is required when the service is Face To Face or BSL';
        $messages['postcode.required_if'] = 'The postcode field is required when the service is Face To Face or BSL';

        return $messages;
    }

    public function withValidator($validator)
    {
        $validator->sometimes('contact_information', 'required', function () {
            if ((int) $this->skill_id !== 1) {
                return false;
            }

            if ((bool) $this->contact_information_is_same_as_account) {
                return false;
            }

            return true;
        });
    }
}
