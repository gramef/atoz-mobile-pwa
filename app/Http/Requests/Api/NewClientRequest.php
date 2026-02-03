<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\ClientRequest;
use App\Http\Requests\InterpreterJobRequest;
use App\Http\Requests\TranslatorJobRequest;
use Illuminate\Foundation\Http\FormRequest;

class NewClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = (new ClientRequest)->rules();

        if ($this->job_type === 'interpreter') {
            return array_merge(
                $rules,
                (new InterpreterJobRequest)->rules()
            );
        }

        return array_merge(
            $rules,
            (new TranslatorJobRequest)->rules()
        );
    }
}
