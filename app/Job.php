<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    public function matchedAgents()
    {
        return $this->morphMany('App\MatchedAgent', 'job');
    }

    public function quotedMatchedAgents()
    {
        return $this->matchedAgents()->whereIn('status', [2, 4])->has('quotes');
    }

    public function assignedMatched()
    {
        return  $this->morphOne('App\MatchedAgent', 'job')->where('status', 4);
        //print_r($var->toSql());
        //print_r($var->getBindings());
        //exit;
    }

    public function skill()
    {
        return $this->belongsTo('App\Skill', 'skill_id');
    }

    public function agent()
    {
        return $this->belongsTo('App\Agent');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function toLanguage()
    {
        return $this->belongsTo('App\Language', 'to_language_id')->withTrashed();
    }

    public function cancellation()
    {
        return $this->morphOne('App\Cancellation', 'job');
    }

    public function getReferenceAttribute()
    {
        return "$this->id/atoz";
    }

    public function scopeHasEnabledUser($query)
    {
        $query->whereHas('client.user', function ($q) {
            return $q->where('enabled', 1);
        });
    }

    public function getStatusNameAttribute()
    {
        return config('enums.statuses')[$this->status] ?? 'pending';
    }

    public function scopeFilter($query, array $filters)
    {

        $t = $filters;

        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('id', 'like', '%' . preg_replace('/\/.*/', '', $search) . '%');
        })
        ->when($filters['language_id'] ?? null, function ($query, $language) {
            $query->where('to_language_id', $language);
        })
        ->when($filters['client'] ?? null, function ($query, $client) {
            $query->whereHas('client', function ($q) use ($client) {
                $q->where('id', $client);
            });
        })
  ->when($filters['require_qualified'] ?? null, function ($query, $require_qualified) {
      $query->where('require_qualified', $require_qualified);
  })
          ->when($filters['dna'] ?? null, function ($query, $dna) {
              $query->where('dna', $dna);
          })
              ->when($filters['retrn'] ?? null, function ($query, $retrn) {
                  $query->where('retrn', $retrn);
              })
        ->when($filters['company'] ?? null, function ($query, $company) {
            $query->whereHas('client.organisation.company', function ($query) use ($company) {
                $query->where('id', $company);
            });
        })
                ->when($filters['skills'] ?? null, function ($query, $skills) {
                    $query->whereHas('skills', function ($query) use ($skills) {
                        $query->where('id', $skills);
                    });
                })

        ->when($filters['agents'] ?? null, function ($query, $agents) {
            $query->whereHas('agent.user', function ($query) use ($agents) {
                $query->where('id', $agents);
            });
        })

        ->when($filters['date'] ?? null, function ($query, $date) {
            $dateColumn = get_class($this) == 'App\InterpreterJob' ? 'appointment_date' : 'target_date';

            if (strpos($date, 'to') === false) {
                return $query->where($dateColumn, $date);
            }

            $query->dateBetween($dateColumn, $date);
        })
        ->when($filters['bulk_id'] ?? null, function ($query, $bulkId) {
            $query->where('bulk_id', $bulkId);
        });


        if (isset($filters['status']) && $filters['status'] !== null) {
            $query->where('status', $filters['status']);
        }
    }

    public function scopeDateBetween($query, string $dateColumn, string $date)
    {
        $query->where([
            [ $dateColumn, '>=', explode(' to ', $date)[0] ],
            [ $dateColumn, '<=', explode(' to ', $date)[1] ]
        ]);
    }

    public function matchedLoggedInAgent()
    {
        return $this->matchedAgents->where('agent_id', optional(auth()->user()->agent)->id)->first();
    }

    public function canBeQuotedByUser(User $user): bool
    {
        if ($user->hasRole('admin')) {
            return $this->statusName === 'pending' && $this->quotedMatchedAgents->isNotEmpty() && $this->adminQuotes->isEmpty();
        }

        if ($user->hasRole('agent')) {
            return $this->statusName === 'pending' && $this->matchedLoggedInAgent()->quotes->isEmpty();
        }

        return false;
    }

    public function getStatusForAgent()
    {
        $matchedAgent = $this->matchedLoggedInAgent();
        
        if (!$matchedAgent) {
            return null;
        }
        
        // If the job is not assigned to this agent (agent_id is null or different), 
        // and the matched agent was previously assigned, show as cancelled
        if ($matchedAgent->status == 'assigned' && $this->agent_id != optional(auth()->user()->agent)->id) {
            return 'cancelled';
        }
        
        $status = $matchedAgent->status;
        return $status == 'accepted' ? 'quoted' : $status;
    }

    public function getMatchedAgentIdAttribute()
    {
        return optional($this->matchedLoggedInAgent())->id;
    }

    public function isAssignedToAgent(int $agentId)
    {
        return optional($this->assignedMatched)->id == $agentId;
    }

    public function adminQuotes()
    {
        return $this->morphMany('App\AdminQuote', 'job');
    }

    public function quotesVisibleToUser(User $user)
    {
        return $user->hasAnyRole(['admin', 'client']) ?
            $this->adminQuotes :
            $this->matchedLoggedInAgent()->quotes;
    }

    public function scopeVisibleToUser($query, User $user)
    {
        $query->when($user->hasRole('admin'), function ($query) {
            $query->with('cancellation');
        })
        ->when($user->hasRole('client'), function ($query) use ($user) {
            $query->where('client_id', $user->client->id);
        })
        ->when($user->hasRole('agent'), function ($query) use ($user) {
            $query->active()
                ->matchedToAgent($user->agent)
                  ->orWhere('agent_id', $user->agent->id);
        });
    }
    public function scopeMatchedToAgent($query, Agent $agent)
    {
        $query->whereDoesntHave('agent')

            ->whereHas('matchedAgents', function ($q) use ($agent) {
                $q->where('agent_id', $agent->id);
                //      ->whereNotIn('status', [ 1,3 ]);
            });

        //   ->excludeDoubleBookings($agent);
    }

    public function scopeExcludeDoubleBookings($query, Agent $agent)
    {
        $assignedInterpreterJobs = $agent->interpreterJobs;

        foreach ($assignedInterpreterJobs as $existingJob) {
            $query->whereDoesntHave('matchedAgents.interpreterJobs', function ($q) use ($existingJob) {

                $q->where('appointment_date', $existingJob->appointment_date)

                    ->where(function ($q) use ($existingJob) {

                        $q->orWhere(function ($q) use ($existingJob) {
                            // $q->where('start_time', '>', $existingJob->start_time);

                            $q->where([
                                ['start_time', '<=', $existingJob->start_time],
                                ['end_time', '>', $existingJob->start_time],
                            ])

                            ->orWhere([
                                ['start_time', '>=', $existingJob->start_time],
                                ['end_time', '<=', $existingJob->end_time],
                            ])

                            ->orWhere([
                                ['start_time', '<', $existingJob->end_time],
                                ['end_time', '>=', $existingJob->end_time],
                            ]);


                        });
                    });
            });

        }

    }

    public function isVisible($user)
    {
        return self::visibleToUser($user)->where(self::getKeyName(), $this->getKey())->exists();
    }
    public function isShowable($user)
    {
        return self::showableToUser($user)->where(self::getKeyName(), $this->getKey())->exists();
    }
    public function scopeShowableToUser($query, User $user)
    {
        $query->when($user->hasRole('admin'), function ($query) {
            $query->with('cancellation');
        })
        ->when($user->hasRole('client'), function ($query) use ($user) {
            $query->where('client_id', $user->client->id);
        })
        ->when($user->hasRole('agent'), function ($query) use ($user) {
            $query->active()
                ->showToAgent($user->agent)
                   ->orWhere('agent_id', $user->agent->id);
        });
    }
    public function scopeShowToAgent($query, Agent $agent)
    {
        $query->whereDoesntHave('agent')

         ->whereHas('matchedAgents', function ($q) use ($agent) {
             $q->where('agent_id', $agent->id)
              ->whereNotIn('status', [ 3 ]);
         })

          ->excludeDoubleBookings($agent);
    }
    public function canBeCancelled(): bool
    {
        return !$this->cannotBeCancelled();
    }

    public function cannotBeCancelled(): bool
    {
        if (class_basename($this) == 'InterpreterJob') {
            $currentDate = Carbon::parse($this->appointment_date->setTimeFromTimeString($this->start_time))->timezone('Europe/London')
            ;
        } else {
            $currentDate = Carbon::parse($this->target_date)->timezone('Europe/London')
            ;
        }
        $elapsed = false;
        if ($currentDate < Carbon::now()) {
            $elapsed = true;
        }
        // }    else{
        //     $elapsed=true;
        // }

        return in_array($this->statusName, ['cancelled', 'rejected', 'completed']) || $elapsed == true;
    }

    public function cancel(?string $message): void
    {
        $this->update([
            'agent_id' => null,
            'status' => 2,
            'cancelled_at' => now(),
        ]);

        if ($message) {
            $this->cancellation()->create([
                'message' => $message,
            ]);
        }
    }
    public function securityType()
    {

        return $this->belongsTo(SecurityType::class);
    }
    public function interpreterType()
    {
        return $this->belongsTo(interpreterType::class);
    }

}

?>