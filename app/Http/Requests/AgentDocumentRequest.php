<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgentDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // File validation for documents
            'documents.*' => ['mimes:png,jpg,jpeg,doc,docx,pdf,bin'],
            'dbs_update_reference_number' => ['nullable', 'string', 'max:255'],
            'induction_date' => ['date'],
            'dbs_number' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator)
    {
        // Apply 'dbs_expiry_date' validation only for agents
        $validator->sometimes('dbs_expiry_date', ['nullable', 'date', 'after:today'], function () {
            return $this->user()->hasRole('agent'); // Only apply this rule for agents
        });

        // Ensure dbs_number is present but nullable
        $validator->sometimes(['dbs_expiry_date', 'dbs_number'], 'present|nullable', function ($input) {
            return true; // Always apply
        });

        // Make induction_date required for non-agents
        $validator->sometimes('induction_date', 'required', function () {
            return !$this->user()->hasRole('agent'); // Required for non-agents
        });
    }
}
