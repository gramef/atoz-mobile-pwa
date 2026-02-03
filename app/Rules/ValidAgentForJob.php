<?php

namespace App\Rules;

use App\Agent;
use App\InterpreterJob;
use App\TranslatorJob;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Request;

class ValidAgentForJob implements Rule
{
    protected $type;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($type = null)
    {
        $this->type = $type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $translatorJob = TranslatorJob::make(Request::all());
        $interpreterJob = InterpreterJob::make(Request::all());
        //   dd($interpreterJob);
        switch ($this->type) {
            case 'interpreter':
                return Agent::matchesInterpreterJob($interpreterJob)
                 // ->canBeMatchedToJobs()
                  ->canBeMatchedToInterpreterJobs($interpreterJob)
                    ->where('id', $value)
                    ->exists();

            case 'translator':
                return Agent::matchesTranslatorJob(
                    $translatorJob
                )
                   // ->canBeMatchedToJobs()
                    ->canBeMatchedToTranslatorJobs()
                    ->where('id', $value)
                    ->exists();

            default:
                return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The given agent is not valid for this job';
    }
}
