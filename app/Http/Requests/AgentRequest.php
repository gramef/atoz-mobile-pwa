<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AgentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if (!$this->user()->hasRole('admin')) {
            $this->request->remove('restrict_job_notifications');
        }
    }

    public function rules()
    {
        return [
            'title' => ['required', Rule::in(array_keys(config('enums.titles')))],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'agent_email'],
            'password' => ['sometimes', 'nullable', 'string', 'min:6', 'max:255'],
            'contact_number' => ['required', 'string', 'max:255'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'county' => ['required', 'string', 'max:255'],
            'postcode' => ['required', 'string', 'max:255'],
//            'interpreter_types' => ['array', 'cannot_be_community_and_qualified'],
            'interpreter_types.*' => ['sometimes', 'exists:roles,name'],
            'gender' => [Rule::in(array_keys(config('enums.genders')))],
            'skills' => ['array'],
            'skills.*' => ['sometimes', 'exists:skills,id'],
            'languages' => ['array'],
            'skype_details' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['sometimes', 'string', 'max:255'],
            'contact_method' => ['sometimes', 'array'],
            'contact_method.*' => ['sometimes', 'exists:contact_methods,id'],
            'restrict_job_notifications' => ['sometimes', 'boolean'],
            'can_provide_affidavit' => ['sometimes', 'boolean'],
            'can_provide_affirmation' => ['sometimes', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'profile_picture.required' => 'You must upload a profile picture',
            'profile_picture.string' => 'You must upload a profile picture',
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes(['interpreter_types', 'gender', 'skills', 'languages', 'contact_method'], 'required', function() {
            if ($this->user()->hasAnyRole('agent')) {
                return false;
            }

            return true;
        });

        $validator->addExtension('agent_email', function($attribute, $value) {
            if ($this->method() === 'POST') {

                /** new agent is setting up their profile, ignore their existing user */
                if ($this->user()->hasRole('new-agent')) {
                    $this->validate([
                        $attribute => Rule::unique('users')->ignore($this->user())->where('enabled', 1),
                    ]);

                    return true;
                }

                /** admin is creating an agent, don't ignore any users */
                $this->validate([
                    $attribute => Rule::unique('users')->where('enabled', 1),
                ]);

                return true;
            }

            /** agent is being updated, ignore the user param in the route or the logged in user */
            $this->validate([
                $attribute => Rule::unique('users')->ignore(optional($this->route('agent'))->user ?? $this->user())->where('enabled', 1),
            ]);

            return true;
        });

        $validator->addExtension('cannot_be_community_and_qualified', function($attribute, $value) {
            if (!in_array('qualified-interpreter', $value)) {
                return true;
            }

            if (!in_array('community-interpreter', $value)) {
                return true;
            }

            return false;
        });

        $validator->addReplacer('cannot_be_community_and_qualified', function () {
            return 'You cannot be both a qualified and community interpreter.';
        });
    }
}
