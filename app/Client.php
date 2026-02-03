<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'contact_number',
        'client_address_line_1',
        'client_address_line_2',
        'client_county',
        'client_postcode',
        'seen_terms',
        'archived',
        'always_requires_a_quote',
        'invoice_details_same_as_account',
        'invoice_email_same_as_account',
        'rejected',
        'show_agents'
    ];

    protected $casts = [
        'always_requires_a_quote' => 'boolean',
        'invoice_details_same_as_account' => 'boolean',
        'invoice_email_same_as_account' => 'boolean',
        'archived' => 'boolean',
        'rejected' => 'boolean',
        'show_agents' => 'boolean',
    
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function interpreterJobs()
    {
        return $this->hasMany('App\InterpreterJob');
    }

    public function userSheet()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function translatorJobs()
    {
        return $this->hasMany('App\TranslatorJob');
    }

    public function organisation()
    {
        return $this->hasOne('App\Organisation');
    }

    public function contactMethods()
    {
        return $this->belongsToMany('App\ContactMethod');
    }

    public function hasActiveJobs(): bool
    {
        return $this->interpreterJobs()->active()->exists() || $this->translatorJobs()->active()->exists();
    }

    public function getRequestType(): string
    {
        return $this->organisation ? 'Company' : 'Individual';
    }

    public function getRequestJob()
    {
        return $this->interpreterJobs->first() ?? $this->translatorJobs->first();
    }

    public function scopeHasEnabledUser($query, $clientId = null)
    {
        $query->whereHas('user', function ($query) {
            $query->role('client')
                ->where('enabled', 1);
        })->orWhere('clients.id', $clientId);
    }

    public function scopeFilter($query, array $filters)
    {
        $query
            ->when($filters['company'] ?? null, function ($query, $companyId) {
                $query->whereHas('organisation', function ($query) use ($companyId) {
                    $query->where('company_id', $companyId);
                });
            })
            ->when($filters['search'] ?? null, function ($query, $email) {
                $query->whereHas('user', function ($query) use ($email) {
                    $query->where('email', 'LIKE', "%$email%")
                        ->orWhere('first_name', 'LIKE', "%$email%")
                        ->orWhere('last_name', 'LIKE', "%$email%");
                });
            });
    }

    public function scopeFullNames($query, $clientId = null){
        return $query
            ->hasEnabledUser($clientId)
            ->join('users', 'clients.user_id', 'users.id')
            ->selectRaw("
                CONCAT(`users`.`first_name`, ' ', `users`.`last_name`) AS name,
                `clients`.`id`
            ")
            ->orderBy('users.first_name', 'ASC')
            ->orderBy('users.last_name', 'ASC')
            ->get()
            ->pluck('name', 'id');
    }

}
