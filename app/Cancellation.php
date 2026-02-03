<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cancellation extends Model
{
    protected $fillable = [
        'job_id', 'job_type', 'message',
    ];

    public function job()
    {
        return $this->morphTo();
    }

    public function matchedAgent()
    {
        return $this->belongsTo('App\MatchedAgent');
    }
}
