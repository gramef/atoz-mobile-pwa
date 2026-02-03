<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\InterpreterJob;

class InterpreterType extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'interpreter_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'name',
        'deleted',
        'created_at',
        'updated_at',
    ];
public function interpreterJobs(){

    return $this->hasMany(InterpreterJob::class);
}
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'deleted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
