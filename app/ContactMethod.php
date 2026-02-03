<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactMethod extends Model
{
    protected $fillable = [
        'client_id',
        'contact_method',
    ];

    public function clients()
    {
        return $this->belongsToMany('App\Client');
    }
}
