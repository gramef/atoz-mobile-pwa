<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    protected $fillable = [
        'client_id',
        'company_id',
        'organisation_company',
        'vat_number',
        'company_number',
        'organisation_address_line_1',
        'organisation_address_line_2',
        'organisation_county',
        'organisation_postcode',
        'organisation_email',
    ];

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function company()
    {
        return $this->belongsTo('App\Company')->withTrashed();
    }
}
