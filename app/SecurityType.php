<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\InterpreterJob;

class SecurityType extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'security_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'deleted',
        'details',
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
    ];
}
