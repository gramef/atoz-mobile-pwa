<?php

namespace App;

use App\TranslatorJob;
use App\InterpreterJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Agent extends Model
{
    protected $fillable = [
        'user_id',
        'contact_number',
        'address_line_1',
        'address_line_2',
        'county',
        'postcode',
        'gender',
        'can_provide_affidavit',
        'can_provide_affirmation',
        'dbs_expiry_date',
        'dbs_number',
        'induction_date',
        'dbs_update_reference_number',
        'restrict_job_notifications',
        'skype_details',
        'profile_picture',
        'notified_of_dbs',
        'notified_week_of_dbs',
        'restrict_job_notification_toggle_button_display'
    ];

    protected $casts = [
        'can_provide_affidavit' 		=> 'boolean',
        'can_provide_affirmation' 		=> 'boolean',
        'restrict_job_notifications' 	=> 'boolean',
        'gender'	 		=> 'integer',
        'notified_of_dbs' 	=> 'boolean',
        'notified_week_of_dbs' 			=> 'boolean',
        'restrict_job_notification_toggle_button_display' => 'boolean',
    ];
	
    //Adding agent type by Nomi
    public static $agentTypes = [
        'court-qualified-interpreter' => 'Court Qualified Interpreter',
        'community-interpreter' => 'Community Interpreter',
        'level-community-interpreter' => 'Level 2,3,4 Community Interpreter',
        'qualified-translator' => 'Qualified Translator',
        'translator' => 'Translator',
    ];

    protected $dates = [
        'dbs_expiry_date',
        'induction_date'
    ];

    protected $genders = [
        'Male',
        'Female',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function skills()
    {
        return $this->belongsToMany('App\Skill');
    }

    public function contactMethods()
    {
        return $this->belongsToMany('App\ContactMethod');
    }

    public function languages()
    {
        return $this->belongsToMany('App\Language')->withTrashed();
    }

    public function interpreterJobs()
    {
        return $this->hasMany('App\InterpreterJob');
    }

    public function translatorJobs()
    {
        return $this->hasMany('App\TranslatorJob');
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function matchedJobs()
    {
        return $this->hasMany('App\MatchedAgent');
    }

    public function getAgentType()
    {
        if (!$this->user) {
            return null;
        }

        return join(', ', $this->user->getRoleNames()
            ->filter(function ($role) {
                return !in_array($role, ['new-agent', 'agent']);
            })
            ->map(function ($role) {
                if (isset(self::$agentTypes[$role])) {
                    return self::$agentTypes[$role];
                }
                return false;
            })
            ->toArray());
    }

    public function getProfilePicture()
    {
        return Storage::disk('public')->has($this->profile_picture) ?
            Storage::disk('public')->url($this->profile_picture) : null;
    }

    public function getGenderName()
    {
        return $this->genders[$this->gender];
    }

    public function isMatchedToJob($job)
    {
        return $this->matchedJobs->contains($job->matchedLoggedInAgent());
    }

    public function setLanguages($languages)
    {
        if (!$languages instanceof Illuminate\Support\Collection) {
            $languages = collect($languages);
        }

        return $this->languages()->sync($languages->map(function ($language) {
            return Language::where('id', $language)->exists() ?
            $language : Language::create(['name' => $language])->id;
        }));
    }

    public function scopeNew( $query )
    {
        return $query->whereHas('user', function ($query) {
            $query->role('new-agent');
        });
    }

    public function scopeHasEnabledUser( $query )
    {
        return $query->whereHas('user', function ($query) {
            $query->where( 'enabled', 1 );
        });
    }

    public function scopeHasDisabledUser( $query )
    {
        return $query->whereHas('user', function ($query) {
            $query->where( 'enabled', 0 );
        });
    }

    public function hasDbsFields(): bool
    {
        if ($this->dbs_expiry_date === null) {
            return false;
        }

        if ($this->dbs_number === null) {
            return false;
        }

        if ($this->induction_date === null) {
            return false;
        }

        return true;
    }

    public function scopeFilter($query, array $filters)
    {
        $query
            ->when($filters['language'] ?? null, function ($query, $languageId) {
                $query->whereHas('languages', function ($q) use ($languageId) {
                    $q->where('language_id', $languageId);
                });
            })
            ->when($filters['agent_type'] ?? null, function ($query, $agentType) {
                $query->whereHas('user', function ($q) use ($agentType) {
                    $q->role($agentType);
                });
            })
            ->when($filters['name'] ?? null, function ($query, $name) {
                $query->whereHas('user', function ($q) use ($name) {
                    $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE convert(? using utf8mb4) collate utf8mb4_general_ci", ["%$name%"]);
                });
            })
            ->when($filters['email'] ?? null, function ($query, $email) {
                $query->whereHas('user', function ($q) use ($email) {
                    $q->where('email', 'LIKE', "%$email%");
                });
            });
    }

    // public function scopeCanBeMatchedToJobs($query)
    // {
    //     $query
    //         ->where('restrict_job_notifications', false)
    //         ->where('dbs_expiry_date', '>=', today())
    //         ->HasDbsFields()
    //         ->HasRequiredDocuments();




    // }
    /*
        public function scopeCanBeMatchedToJobs($query)
        {
            //  if($job->security_type_id===3){
            $query->where(function ($subQuery) {
                // Agents with DBS details and all required documents
                $subQuery->where('restrict_job_notifications', false)
                         ->where('dbs_expiry_date', '>=', today())
                         ->HasDbsFields()
                         ->HasRequiredDocuments();
            })
         ->orWhere(function ($subQuery) {
             // Agents without DBS details but with basic documents
             $subQuery->where('restrict_job_notifications', false)
                      ->HasRequiredDocumentsWithoutDbs();
         });
            //  }
            //  else{

            //             $query->where(function($subQuery) {
            //     // Agents with DBS details and all required documents
            //     $subQuery->where('restrict_job_notifications', false)
            //              ->where('dbs_expiry_date', '>=', today())
            //              ->HasDbsFields()
            //              ->HasRequiredDocuments();
            // });



            //  }
            return $query;
        }

        */
    public function scopeCanBeMatchedToTranslatorJobs($query)
    {
        //  if($job->security_type_id===3){
        $query->where(function ($subQuery) {
            // Agents with DBS details and all required documents
            $subQuery->where('restrict_job_notifications', false)
                     ->where('dbs_expiry_date', '>=', today())
                     ->HasDbsFields()
                     ->HasRequiredDocuments();
        })
     ->orWhere(function ($subQuery) {
         // Agents without DBS details but with basic documents
         $subQuery->where('restrict_job_notifications', false)
                  ->HasRequiredDocumentsWithoutDbs();
     });
        return $query;
    }


    //Edited by Sanmi to dbs check for non dbs jobs
    public function scopeCanBeMatchedToInterpreterJobs( $query, InterpreterJob $job)
    {
        // dd("Job security_type_id: " . $job->security_type_id);
        /*
                $query->where('restrict_job_notifications', false);

                // If job requires DBS (security_type_id !== 4), filter only agents with DBS
                if ($job->security_type_id !== 4) {
                    return $query->where('dbs_expiry_date', '>=', today())
                                 ->HasDbsFields()
                                 ->HasRequiredDocuments();
                }

                // If job does NOT require DBS, allow both DBS and non-DBS agents
                return $query->where(function ($subQuery) {
                    $subQuery->where('dbs_expiry_date', '>=', today())
                             ->HasDbsFields()
                             ->HasRequiredDocuments()
                             ->orWhere(fn ($innerQuery) => $innerQuery->HasRequiredDocumentsWithoutDbs());
                });

        */

        // Base condition: Exclude agents with job notifications restricted
        $query->where( 'restrict_job_notifications', false );

        // If job does NOT require DBS (security_type_id === 4), return ALL agents
        if ($job->security_type_id === 4) {
            return $query; // No additional filtering
        }

        // If job requires DBS (security_type_id !== 4), filter only agents with DBS
        return $query->where( 'dbs_expiry_date', '>=', today())
                     ->HasDbsFields()
                     ->HasRequiredDocuments();
    }

    /*
    public function scopeCanBeMatchedToInterpreterJobs($query, InterpreterJob $job)
    {
        // Debugging: Check the security_type_id
        //   dd("Job security_type_id: " . $job->security_type_id);

        // Check if the job security requires DBS (e.g., security_type_id !== 4)
        if ($job->security_type_id !== 4) {
            // Agents with DBS details and all required documents
            $query->where(function ($subQuery) {
                $subQuery->where('restrict_job_notifications', false)
                         ->where('dbs_expiry_date', '>=', today())
                         ->HasDbsFields() // Check for DBS fields
                         ->HasRequiredDocuments(); // Check for required documents
            });
        } else {
            // Agents with or without DBS details but with basic documents
            $query->where(function ($subQuery) {
                $subQuery->where('restrict_job_notifications', false)
                         ->where('dbs_expiry_date', '>=', today())
                         ->HasDbsFields()  // Check for DBS fields
                         ->HasRequiredDocuments();  // Check for required documents
            })
            ->orWhere(function ($subQuery) {
                $subQuery->where('restrict_job_notifications', false)
                         ->HasRequiredDocumentsWithoutDbs();  // Check for documents without DBS
            });
        }

        // Debugging: Log the generated SQL to check if it's working as expected
        //   Log::debug("Generated SQL: " . $query->toSql());
        return $query;
    }
*/
    public function scopeHasDbsFields($query)
    {
        $query
            ->whereNotNull('dbs_number')
            ->whereNotNull('dbs_expiry_date')
            ->whereNotNull('induction_date');
    }

    public function scopeHasRequiredDocuments($query)
    {
        $query->whereHas('documents', function ($q) {
            $q->where('type', config('enums.document_types')['dbs']);
        })

        ->whereHas('documents', function ($q) {
            $q->where('type', config('enums.document_types')['proof_of_address']);
        })

        ->whereHas('documents', function ($q) {
            $q->where('type', config('enums.document_types')['passport']);
        })

        ->whereHas('documents', function ($q) {
            $q->where('type', config('enums.document_types')['induction']);
        });
    }

    public function scopeHasRequiredDocumentsWithoutDbs($query)
    {
        $query->whereHas('documents', function ($q) {
            $q->where('type', config('enums.document_types')['proof_of_address']);
        })

        ->whereHas('documents', function ($q) {
            $q->where('type', config('enums.document_types')['passport']);
        })

        ->whereHas('documents', function ($q) {
            $q->where('type', config('enums.document_types')['induction']);
        });

    }

    public function scopeHasLanguage($query, $language)
    {
        $query->whereHas('languages', function ($q) use ($language) {
            $q->where('languages.id', $language);
        });
    }

    public function scopeHasSkills($query, array $skills)
    {
        $query->whereHas('skills', function ($q) use ($skills) {
            $q->whereIn('skills.id', $skills);
        });
    }

    public function scopeCourtQualified($query)
    {
        $query->whereHas('user', function ($q) {
            $q->role('court-qualified-interpreter');
        });
    }
    public function scopeLevelCommunity($query)
    {
        $query->whereHas('user', function ($q) {
            $q->role('level-community-interpreter');
        });
    }
    public function scopeQualifiedTranslator($query)
    {
        $query->whereHas('user', function ($q) {
            $q->role('qualified-translator');
        });
    }

    public function scopeCommunity($query)
    {
        $query
            ->whereHas('user', function ($q) {
                $q->role('community-interpreter');
            });
    }

    /*   public function scopeHasNotBookedDate($query, InterpreterJob $interpreterJob)
       {
           $query->whereDoesntHave('interpreterJobs', function ($q) use ($interpreterJob) {
               $q->when(optional($interpreterJob->agent)->id, function ($q) use ($interpreterJob) {
                   return $q->where('agent_id', optional($interpreterJob->agent)->id);
               })
                   ->where('appointment_date', $interpreterJob->appointment_date)
                   ->where(function ($q) use ($interpreterJob) {
                       $q->where('start_time', '>', $interpreterJob->start_time);
                   });

           });
       }

       */
    public function scopeHasNotBookedDate($query, InterpreterJob $interpreterJob)
    {
        $query->whereDoesntHave('interpreterJobs', function ($q) use ($interpreterJob) {
            $q->when(optional($interpreterJob->agent)->id, function ($q) use ($interpreterJob) {
                return $q->where('agent_id', optional($interpreterJob->agent)->id);
            })
            ->where('appointment_date', $interpreterJob->appointment_date)
            ->where(function ($q) use ($interpreterJob) {
                $q->where('start_time', $interpreterJob->start_time)
                  ->where('status', 1);
            });
        });
    }





    // public function scopeMatchesInterpreterJob($query, InterpreterJob $job)
    // {
    //     $query->hasNotBookedDate($job)
    //         ->hasLanguage($job->to_language_id)
    //         ->hasSkills([$job->skill_id])
    //         ->when($job->require_qualified, function ($q) {
    //             $q->qualified();
    //         })
    //         ->when(!$job->require_qualified, function ($q) {
    //             $q->community();
    //         })
    //         ->when($job->gender != 2, function ($q) use ($job) {
    //             $q->where('gender', $job->gender);
    //         });
    // }
    //updated by muhammad to add additional interpreter types
	
    public function scopeMatchesInterpreterJob($query, InterpreterJob $job)
    {
        $query->hasNotBookedDate($job)
           ->hasLanguage($job->to_language_id)
            ->hasSkills([$job->skill_id])
            ->when($job->require_qualified, function ($q) use ($job) {
                // Apply the appropriate scope based on require_qualified
                switch ($job->require_qualified) {
                    case 1: // Assuming 1 corresponds to 'court-qualified-interpreter'
                        $q->courtQualified();
                        break;
                    case 2:
                        $q->community();
                        break;
                    case 3: // Assuming 2 corresponds to 'level-community-interpreter'
                        $q->levelCommunity();
                        break;
                    case 4: // Assuming 3 corresponds to 'qualified-translator'
                        $q->qualifiedTranslator();
                        break;
                    default:
                        // Handle cases where require_qualified does not match any defined role
                        // Optionally, you can add a default role or throw an exception
                        $q->community(); // This will apply community roles if no match is found
                        break;
                }
            })
            ->when($job->gender != 2, function ($q) use ($job) {
                $q->where('gender', $job->gender);
            });
    }
	
    public function scopeMatchesTranslatorJob($query, TranslatorJob $job)
    {
        $query->hasLanguage($job->from_language_id)
            ->hasLanguage($job->to_language_id)
            ->hasSkills([$job->skill_id, 'translator'])
            ->when($job->affirmation, function ($q) {
                $q->where('can_provide_affirmation', true);
            })
            ->when($job->affidavit, function ($q) {
                $q->where('can_provide_affidavit', true);
            });
    }
	
    /*
        public function scopeWithDistanceFromInterpreterJob($query, InterpreterJob $interpreterJob)
        {
            $query->when($interpreterJob->latitude && $interpreterJob->longitude, function ($q) use ($interpreterJob) {
                $q->selectRaw("agents.*, ( 3959 * acos( cos( radians($interpreterJob->latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($interpreterJob->longitude)
                    ) + sin( radians($interpreterJob->latitude) ) *sin( radians( latitude ) ) )) AS distance");
            });
        }
    */

    public function scopeWithDistanceFromInterpreterJob( $query, InterpreterJob $interpreterJob )
    {
        $query->when($interpreterJob->latitude && $interpreterJob->longitude, function ($q) use ($interpreterJob) {
            $q->selectRaw(
                "agents.*, (3959 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                [$interpreterJob->latitude, $interpreterJob->longitude, $interpreterJob->latitude]
            );
        });
    }

    public function scopeSearchName( $query, $name )
    {
        return $query->whereHas('user', function ($q) use ($name) {
            $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE convert(? using utf8mb4) collate utf8mb4_general_ci", ["%$name%"]);
        });
    }
	
}
