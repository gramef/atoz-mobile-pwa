<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'skill',
        'type',
    ];

    protected $types = [
        0 => 'interpreter',
        1 => 'translator',
    ];

    public $timestamps = false;

    public function getSkillType()
    {
        return $this->types[$this->type];
    }
}
