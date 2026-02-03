<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => ['required', Rule::in(array_keys(config('enums.titles')))],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', $this->method() === 'POST' ? Rule::unique('users')->where('enabled', 1) : Rule::unique('users')->ignore($this->route('admin'))->where('enabled', 1)],
        ];
    }
}
