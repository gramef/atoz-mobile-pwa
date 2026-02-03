<?php

namespace App;

class TranslatorJob extends Job
{
    protected $fillable = [
        'skill_id',
        'client_id',
        'agent_id',
        'from_language_id',
        'to_language_id',
        'requested_agent_id',
        'status',
        'word_count',
        'notes',
        'target_date',
        'client_reference',
        'affirmation',
        'affidavit',
        'cancelled_at',
    ];

    protected $dates = [
        'target_date',
        'cancelled_at'
    ];

    public function fromLanguage()
    {
        return $this->belongsTo('App\Language', 'from_language_id')->withTrashed();
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    public function scopeActive($query)
    {
        $query->where('target_date', '>=', today())
            ->whereNotIn('status', [2, 3, 4]);
    }

    public function isActive(): bool
    {
        if (in_array($this->status, [2, 3, 4])) {
            return false;
        }

        if ($this->target_date->endOfDay()->isFuture()) {
            return true;
        }

        return false;
    }

    public function isWithin24Hours(): bool
    {
        return $this->target_date->subDay()->isPast();
    }

    public function shouldBeRematched(): bool
    {
        if (!$this->agent) {
            return true;
        }

        if (!$this->agent->languages->contains('id', $this->toLanguage->id)) {
            return true;
        }

        if (!$this->agent->languages->contains('id', $this->fromLanguage->id)) {
            return true;
        }

        if (!$this->agent->skills->contains('id', $this->skill->id)) {
            return true;
        }

        if (!$this->agent->can_provide_affirmation && $this->affirmation) {
            return true;
        }

        if (!$this->agent->can_provide_affidavit && $this->affidavit) {
            return true;
        }

        return false;
    }

    public function canBeCompleted(): bool
    {
        if ($this->target_date->endOfDay()->isFuture()) {
            return false;
        }

        if ($this->status === 5) {
            return true;
        }

        return false;
    }

    protected $documentUploaded;

    public function getDocumentUploadedAttribute()
    {
        return $this->documentUploaded;
    }
    public function setDocumentUploadedAttribute($value)
    {
        return $this->documentUploaded = $value;
    }
}