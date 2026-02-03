<?php

namespace App;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Session;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'title',
        'first_name',
        'last_name',
        'email',
        'password',
        'enabled',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['fullName'];

    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    public function agent()
    {
        return $this->hasOne('App\Agent');
    }

    public function client()
    {
        return $this->hasOne('App\Client');
    }

    public function hasNotSeenTerms()
    {
        return false;
        //        return $this->client()->exists() && !$this->client->seen_terms;
    }

    public function getFullNameAttribute() //I'm not sure why this is appended if the method didn't exist
    {
        return $this->getFullName();
    }

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullNameWithTitle()
    {
        return config('enums.titles')[$this->title] . ' ' . $this->first_name . ' ' . $this->last_name;
    }

    public function getUserLevel()
    {
        return $this->getRoleNames()->contains('super-admin') ? 'Super Admin' : $this->getRoleNames()->first();
    }

    public function scopeHasExpiredDBS($query)
    {
        $query->whereHas('agent', function ($query) {
            $query
                ->where('notified_of_dbs', false)
                ->where('dbs_expiry_date', '<=', today()->addMonths(3));
        });
    }


    public function scopeHasExpiredDBS2($query)
    {
        $query->whereHas('agent', function ($query) {
            $query
                ->where('notified_week_of_dbs', false)
                ->where('dbs_expiry_date', '<=', today()->addWeek());
        });
    }


    public function setImpersonating($id)
    {
        Session::put('impersonate', $id);
    }

    public function stopImpersonating()
    {
        Session::forget('impersonate');
    }

    public function isImpersonating()
    {
        return Session::has('impersonate');
    }


}