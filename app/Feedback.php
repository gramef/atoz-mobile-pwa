<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';
    protected $fillable = [
       'agent_id','client_id','job_id','appearance_rating','punctuality','quality_of_interpreting','empathy','comment','status','agent_status'
    ];


    public function interpreterJob()
    {
        return $this->belongsTo(InterpreterJob::class, 'job_id');
    }

    public function interpreter()
    {
        return $this->hasOne(InterpreterJob::class, 'id','job_id');
    }

   public function agent()
   {
      return $this->belongsTo(Agent::class, 'agent_id');
   }

    public function agentOne()
    {
        return $this->hasOne(Agent::class, 'id','agent_id');
    }

    public function feedback()
    {
        return $this->hasOne(InterpreterJob::class, 'id','job_id');
    }

    
}
