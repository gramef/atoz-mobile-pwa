<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MatchedAgent extends Model
{
    protected $fillable = [
        'agent_id',
        'status',
        'distance',
    ];

    private const STATUSES = [
        0 => 'matched',
        1 => 'rejected',
        2 => 'accepted',
        3 => 'cancelled',
        4 => 'assigned',
    ];

    public function getStatusAttribute()
    {
        return self::STATUSES[ $this->attributes['status'] ];
    }

    public function getStatusIdAttribute()
    {
        return $this->attributes['status'];
    }

    public function getFormattedDistance()
    {
        return $this->distance ?
            "{$this->agent->county} / {$this->distance} miles" :
            "{$this->agent->county}/ N.A";
    }

    public function interpreterJobs()
    {
        return $this->belongsTo('App\InterpreterJob', 'job_id');
    }

    public function translatorJobs()
    {
        return $this->belongsTo('App\TranslatorJob', 'job_id');
    }

    public function job()
    {
        return $this->morphTo();
    }

    public function agent()
    {
        return $this->belongsTo('App\Agent');
    }

    public function quotes()
    {
        return $this->hasMany('App\Quote');
    }

    public function cancellation()
    {
        return $this->hasOne('App\Cancellation');
    }

    public function latestQuote()
    {
        return $this->quotes->first();
    }

    public function canBeAssigned()
    {
        // An agent is already assigned to the job
        if ($this->job->assignedMatched) {
            return false;
        }

        // The job does not require a quote
        if ($this->job_type == 'App\InterpreterJob' && !$this->job->client->always_requires_a_quote) {
            return true;
        }

        // The agent has not quoted for the job

        if ($this->status != 'accepted') {
            return false;
        }

        // Admin needs to quote for job before assigning an agent
        if ($this->job->adminQuotes->isEmpty()) {
            return false;
        }

        return true;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, [
            'assigned',
            'accepted',
        ]);
    }

}