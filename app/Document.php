<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'job_id',
        'job_type',
        'url',
        'name',
        'type',
        'documentable_type',
        'documentable_id',
    ];

    protected $appends = ['fullUrl'];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function getFullUrlAttribute()
    {
        return Storage::disk('public')->url($this->url);
    }
}
