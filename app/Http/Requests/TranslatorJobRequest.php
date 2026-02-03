<?php

namespace App\Http\Requests;

use App\Rules\ValidAgentForJob;
use Illuminate\Foundation\Http\FormRequest;

class TranslatorJobRequest extends FormRequest
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
        return [
            'from_language_id' => ['required', 'exists:languages,id'],
            'to_language_id' => ['required', 'exists:languages,id'],
            'requested_agent_id' => ['nullable', 'exists:agents,id', new ValidAgentForJob('translator')],
            'skill_id' => ['required', 'exists:skills,id'],
            'word_count' => ['required', 'integer', 'min:0'],
            'target_date' => ['required', 'date', 'after:yesterday'],
            'client_reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:255'],
            'affirmation' => ['required', 'boolean'],
            'affidavit' => ['required', 'boolean'],
            'client_id' => ['sometimes', 'exists:clients,id'],
            'documents' => ['array'],
            'documents.*.name' => ['string', 'max:255'],
            'documents.*.url' => ['string', 'max:255'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('documents', 'required', function () {
            if ($this->method() !== 'POST') {
                return false;
            }

            return true;
        });
    }
}
