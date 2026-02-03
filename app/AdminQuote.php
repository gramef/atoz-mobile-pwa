<?php

namespace App;

class AdminQuote extends BaseQuote
{
    protected $fillable = [
        'interpreting_cost',
        'travel_time',
        'travel_cost',
        'mileage_miles',
        'mileage_cost',
        'cost_description',
        'cost',
        'status',
    ];

    public function job()
    {
        return $this->morphTo();
    }

    public function getTotalAmountAttribute()
    {
        return '£' . number_format(
            ($this->interpreting_cost * $this->job->totalHours) + 
            ($this->travel_cost * $this->travel_time) + 
            ($this->mileage_cost * $this->mileage_miles) + 
            $this->cost
        , 2);
    }
}
