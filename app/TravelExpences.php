<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TravelExpences extends Model
{
    protected $table = 'travel_expences';
    protected $fillable = [
       'travel_start_time', 'travel_end_time', 'travel_amount', 'job_id', 'agent_id', 'travel_date', 'status'
    ];
    
}
