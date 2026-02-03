<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseQuote extends Model
{
    protected const STATUSES = [
        0 => 'quote sent',
        1 => 'quote accepted',
        2 => 'quote rejected',
    ];

    protected $appends = [
        'totalAmount'
    ];

    public function getStatusAttribute()
    {
        return self::STATUSES[ $this->attributes['status'] ];
    }

    public function getStatusIdAttribute()
    {
        return $this->attributes['status'];
    }

    public function getStatusForUser()
    {
        return auth()->user()->hasRole('client') && $this->status == 'quote sent' ? 
            'quoted recieved' : 
            $this->status;
    }
}
