<?php

namespace App\Http\Requests\Api;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FindAgentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if($this->route()->parameter('job_type') == 'interpreter'){
            $this->merge([
                'end_time' => Carbon::parse($this->request->get('start_time'))
                                ->addHours($this->request->get('duration_hours'))
                                ->addMinutes($this->request->get('duration_minutes'))
                                ->format('H:i:s')
            ]);
        }

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch($this->route()->parameter('job_type')) {
            case 'interpreter':
                return [
                    'to_language_id'    => [ 'required', 'exists:languages,id' ],
                    'skill_id'          => [ 'required', 'exists:skills,id' ],
                    'require_qualified' => [ 'required', 'integer' ],
                    'gender'            => [ 'required', Rule::in(array_keys(config('enums.genders'))) ],
                    'appointment_date'  => [ 'required', 'date' ],
                    'start_time'        => [ 'required', 'date_format:H:i:s' ],
                    'duration_hours'    => [ 'required' ],
                    'duration_minutes'  => [ 'required' ]

                    //@NOTE: Could add longitude and latitude here
                ];

            case 'translator':
                return [
                    'from_language_id' => ['required', 'exists:languages,id'],
                    'to_language_id'   => ['required', 'exists:languages,id'],
                    'skill_id'         => ['required', 'exists:skills,id'],
                    'affirmation'      => ['required', 'boolean'],
                    'affidavit'        => ['required', 'boolean']
                ];
        }
    }
}
