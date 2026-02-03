<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'is_organisation' => ['sometimes', 'boolean'],
            'title' => ['required', Rule::in(array_keys(config('enums.titles')))],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:255'],
            'contact_method' => ['sometimes', 'array'],
            'contact_method.*' => ['exists:contact_methods,id'],
            'client_address_line_1' => ['required', 'string', 'max:255'],
            'client_address_line_2' => ['nullable', 'string', 'max:255'],
            'client_county' => ['required', 'string', 'max:255'],
            'client_postcode' => ['required', 'string', 'max:255'],
            'always_requires_a_quote' => ['sometimes', 'boolean'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore(
                    $this->method() === 'POST' ? null : optional($this->route('client'))->user->id
                )
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:6', 'max:255'],
            'invoice_details_same_as_account' => ['sometimes', 'boolean'],
            'invoice_email_same_as_account' => ['sometimes', 'boolean'],
            'vat_number' => ['nullable', 'string', 'max:255'],
            'company_number' => ['required_if:is_organisation,1', 'nullable', 'string', 'max:255'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'organisation_company' => ['required_if:is_organisation,1', 'nullable', 'string', 'max:255'],
            'organisation_address_line_1' => ['nullable', 'string', 'max:255'],
            'organisation_address_line_2' => ['nullable', 'string', 'max:255'],
            'organisation_county' => ['nullable', 'string', 'max:255'],
            'organisation_postcode' => ['nullable', 'string', 'max:255'],
            'organisation_email' => ['nullable', 'email', 'max:255'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes(['organisation_address_line_1', 'organisation_county', 'organisation_postcode'], 'required', function() {
            if ((bool) !$this->is_organisation) {
                return false;
            }

            if ((bool) $this->invoice_details_same_as_account) {
                return false;
            }

            return true;
        });

        $validator->sometimes('organisation_email', 'required', function() {
            if ((bool) !$this->is_organisation) {
                return false;
            }

            if ((bool) $this->invoice_email_same_as_account) {
                return false;
            }

            return true;
        });
    }
}
