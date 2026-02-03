<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Language;
use Carbon\Carbon;
use App\Agent;
use App\User;

class AllUpdates extends InterpreterJob
{
    protected $table = 'allupdates';
    protected $fillable = [
        'user_id', 
        'user_type', 
        'job_id', 
        'job_type',
        'agent_id', 
        'new_status', 
        'code', 
        'update_time', 
        'update_date', 
        'comment', 
        'deleted',
        'created_at',
        'updated_at'
        
    
];
public function agent()
{
    return $this->belongsTo('App\Agent', 'agent_id');
}
public function user()
{
    return $this->belongsTo('App\User', 'user_id');
}

public function getUserDetailsAttribute()
{
    

    $user = User::find($this->user_id);

    // Return full name if user exists
    if ($user) {
        return $user->first_name . ' ' . $user->last_name;
    }

    // Return a default value or null if no user found
    return null;
}
}

