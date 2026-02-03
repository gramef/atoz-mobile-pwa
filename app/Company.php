<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'vat_number',
        'company_number',
        'address_line_1',
        'address_line_2',
        'county',
        'postcode'
    ];

    protected $dates = ['deleted_at'];

    public function resolveRouteBinding($value)
    {
        return in_array(SoftDeletes::class, class_uses($this))
            ? $this->where($this->getRouteKeyName(), $value)->withTrashed()->first()
            : parent::resolveRouteBinding($value);
    }

    public function organisations()
    {
        return $this->hasMany('App\Organisation');
    }


    public function scopeFilter($query, array $filters)
    {
        $query
            ->when($filters['name'] ?? null, function ($query, $name) {
                $query->where('name', 'LIKE', '%' . $name . '%');
            })
            ;
    }
}
