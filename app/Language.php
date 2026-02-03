<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
    ];

    public function agents()
    {
        return $this->belongsToMany('App\Agent');
    }
    public function fromInterpreterJob()
    {
        return $this->hasMany('App\InterpreterJob', 'from_language_id')->active();
    }
    public function toInterpreterJob()
    {
        return $this->hasMany('App\InterpreterJob', 'to_language_id')->active();
    }
    public function fromTranslatorJob()
    {
        return $this->hasMany('App\TranslatorJob', 'from_language_id')->active();
    }
    public function toTranslatorJob()
    {
        return $this->hasMany('App\TranslatorJob', 'to_language_id')->active();
    }

    public function getActiveJobCountAttribute() {

        return $this->fromInterpreterJob()->count() + $this->toInterpreterJob()->count() + $this->fromTranslatorJob()->count() + $this->toTranslatorJob()->count();

    }
}
