<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['nullable', 'string', 'email', 'max:255', $this->method() === 'POST' ? Rule::unique('companies') : Rule::unique('companies')->ignore($this->route('company')->id)],

            'vat_number'     => ['nullable', 'string', 'max:255'],
            'company_number' => ['nullable', 'string', 'max:255'],

            'address_line_1' => ['required_if:skill_id, 1, 2', 'nullable', 'max:255'],
            'address_line_2' => ['nullable', 'max:255'],
            'county'         => ['nullable', 'max:255'],
            'postcode'       => ['nullable', 'max:255'],
        ];
    }
}
