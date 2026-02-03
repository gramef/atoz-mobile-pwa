<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Timesheet extends Model
{
    protected $fillable = [
        'agent_id',
        'job_id',
        'status',
        'agent_signature',
        'client_signature',
        'client_status',
        'agent_status',
        'agent_duration_hours',
        'agent_duration_minutes',
        'client_duration_hours',
        'client_duration_minutes',
        'agent_start_time',
        'agent_end_time',
        'client_phone',
        'client_name',
        'client_designation'
     ];

    public function getAgentStartTimeAttribute($value)
    {
        return Carbon::parse($value)->format('H:i');
    }

    // Accessor for agent_end_time
    public function getAgentEndTimeAttribute($value)
    {
        return Carbon::parse($value)->format('H:i');
    }
    public function interpreterJob()
    {
        return $this->belongsTo(InterpreterJob::class, 'job_id');
    }

    public function interpreter()
    {
        return $this->hasOne(InterpreterJob::class, 'id', 'job_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }

    public function agentOne()
    {
        return $this->hasOne(Agent::class, 'id', 'agent_id');
    }

    // Define a scope for filtering by client name
    public function scopeFilterByClientName($query, $clientName)
    {
        return $query->whereHas('interpreter.client', function ($query) use ($clientName) {
            $query->whereHas('userSheet', function ($query) use ($clientName) {
                $query->where('first_name', 'like', '%' . $clientName . '%')
                      ->orWhere('last_name', 'like', '%' . $clientName . '%');
            });
        });
    }

    // Define a scope for filtering by agent name
    public function scopeFilterByAgentName($query, $agentName)
    {
        return $query->whereHas('agentOne.user', function ($query) use ($agentName) {
            $query->where('first_name', 'like', '%' . $agentName . '%')
                  ->orWhere('last_name', 'like', '%' . $agentName . '%');
        });
    }

    // Define a scope for filtering by ref (job_id)
    public function scopeFilterByRef($query, $ref)
    {
        return $query->whereHas('interpreterJob', function ($query) use ($ref) {
            $query->where('id', 'like', '%' . $ref . '%'); // job_id field
        });
    }

}
