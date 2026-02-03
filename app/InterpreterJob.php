<?php

namespace App;

use App\Language;
use Carbon\Carbon;
use App\Collections\Collection;
use Log;

class InterpreterJob extends Job
{
    protected $fillable = [
        'client_id',
        'agent_id',
        'skill_id',
        'require_qualified',
        'security_type_id',
        'from_language_id',
        'to_language_id',
        'requested_agent_id',
        'status',
        'gender',
        'special_requirements',
        'contact_information',
        'personal_identity_number',
        'contact_person_details',
        'user_title',
        'user_first_name',
        'user_last_name',
        'client_reference',
        'appointment_date',
        'start_time',
        'end_time',
        'department',
        'address_line_1',
        'address_line_2',
        'county',
        'postcode',
        'file_reference',
        'date_of_birth',
        'latitude',
        'longitude',
        'duration',
        'duration_minutes',
        'duration_hours',
        'service_user_required',
        'contact_information_is_same_as_account',
        'cancelled_at',
        'dna','retrn',
        'bulk_id',
    ];

    protected $dates = [
        'appointment_date',
        'date_of_birth',
        'cancelled_at',
    ];

    protected $casts = [
        'service_user_required' => 'boolean',
        'contact_information_is_same_as_account' => 'boolean',
        'require_qualified' => 'integer',
        'gender' => 'integer',
        'dna' => 'boolean',
        'retrn' => 'boolean'
    ];

    protected $appends = [
        'fromLanguage',
        'formattedDuration',
        'totalHours'
    ];

    public function requiresQuote(): bool
    {
        return $this->client->always_requires_a_quote;
    }

    public function getGenderName()
    {
        return config('enums.genders')[$this->gender];
    }

    public function getFromLanguageAttribute()
    {
        return new Language(['name' => 'English', 'id' => 37]);
    }
    public function getFormattedDurationAttribute()
    {
        $hours = $this->duration_hours ?? 0;
        $minutes = $this->duration_minutes ?? 0;

        return "$hours hours $minutes minutes";
    }

    /* public function getFormattedDurationAttribute()
    {
        return "$this->duration_hours hours $this->duration_minutes minutes";
    }
 public function getStartTimeAttribute($startTime)
      {
          return Carbon::parse($startTime)->format('H:i');
      }*/
    public function getStartTimeAttribute($startTime)
    {
        return $startTime ? Carbon::parse($startTime)->format('H:i') : null;
    }
    public function getEndTimeAttribute($endTime)
    {
        return Carbon::parse($endTime)->format('H:i');
    }

    public function getTotalHoursAttribute()
    {
        return ($this->duration_minutes / 60) + $this->duration_hours;
    }

    public function scopeActive($query)
    {
        //  $query->where('appointment_date', '>=', today());
        $query  ->whereNotIn('status', [2, 3, 4]);
    }

    public function isActive(): bool
    {
        if (in_array($this->status, [2, 3, 4])) {
            return false;
        }

        /*
        if ($this->appointment_date->endOfDay()->isFuture()) {
            return true;
        }
*/

        // return false;
        return true;
    }

    public function isWithin24Hours(): bool
    {
        return $this->appointment_date->setTimeFromTimeString($this->start_time)->subDay()->isPast();
    }

    //added for 24 hours remaining notifying agent
    public static function getUsersOfUpcomingJobs(): EloquentCollection
    {
        // Get the current time in London timezone
        $now = Carbon::now()->timezone('Europe/London');

        return $this->isExactly24HoursRemaining();
    }

    // Instance method to check if the job has exactly 24 hours remaining
    public function isExactly24HoursRemaining(): bool
    {

        $now = Carbon::now()->timezone('Europe/London');
        $startDateTime = Carbon::parse($this->appointment_date->setTimeFromTimeString($this->start_time))->timezone('Europe/London');
        $diffInMinutes = $startDateTime->diffInMinutes($now, false);

        // Log the relevant times and difference
        Log::info("Checking job ID: {$this->id}");
        Log::info("Current time: {$now}");
        Log::info("Start time: {$startDateTime}");
        Log::info("Difference in minutes: {$diffInMinutes}");

        // Return true if the difference is within a 5-minute tolerance
        return $diffInMinutes >= -1445 && $diffInMinutes <= -1435;
    }


    //function created by muhammad nouman
    public function getInterpreterRole()
    {
        $roleMapping = [
            1 => 'court-qualified-interpreter',
            2 => 'community-interpreter',
            3 => 'level-community-interpreter',
            4 => 'qualified-translator',
        ];

        return $roleMapping[$this->require_qualified] ?? 'default-role';
    }
    public function shouldBeRematched(): bool
    {
        $interpreterRole = $this->getInterpreterRole();
        if (!$this->agent) {
            return true;
        }

        if (!$this->agent->languages->contains('id', $this->toLanguage->id)) {
            return true;
        }

        if (!$this->agent->skills->contains('id', $this->skill->id)) {
            return true;
        }

        if (!$this->agent->user->hasRole($interpreterRole)) {
            return true;
        }

        if ($this->gender !== 2 && $this->agent->gender !== $this->gender) {
            return true;
        }

        return false;
    }
    public function canBeDna(): bool
    {
        if ($this->dna) {

            return false;
        }
        if (!in_array($this->status, [1,5])) {
            return false;
        }
        $now = Carbon::now();
        $appointmentDateTime = Carbon::parse($this->appointment_date->setTimeFromTimeString($this->start_time))->timezone('Europe/London');
        if (!($appointmentDateTime < $now)) {
            return false;
        }
        return true;
    }
    public function canBeRetrn(): bool
    {
        if ($this->retrn) {

            return false;
        }
        if (!in_array($this->status, [1,5])) {
            return false;
        }
        $now = Carbon::now();
        $appointmentDateTime = Carbon::parse($this->appointment_date->setTimeFromTimeString($this->start_time))->timezone('Europe/London');
        if (!($appointmentDateTime < $now)) {
            return false;
        }
        return true;
    }
    public function canBeCompleted(): bool
    {

        $now = Carbon::now('Europe/London');


        $appointmentDate = Carbon::parse($this->appointment_date, 'Europe/London'); // Assuming $this->appointment_date is a date string
        $startTime = Carbon::parse($this->start_time, 'Europe/London'); // Assuming $this->start_time is a time string

        // Combine appointment date with start time to get the full DateTime in UK timezone
        $appointmentDateTime = $appointmentDate->copy()->setTime($startTime->hour, $startTime->minute, $startTime->second);

        // Check if the appointment datetime is in the future or if the job's status is not within the allowed range
        //  if ($appointmentDateTime->isFuture() || !in_array($this->status, [1, 5])) {
        //    return false;
        //}


        if (!in_array($this->status, [1, 5])) {
            return false;
        }

        if ($this->requiresQuote() && $this->status !== 5) {
            return false;
        }

        return true;
    }

    public function hasAddressFields(): bool
    {
        return $this->postcode || $this->address_line_1 || $this->address_line_2 || $this->county;
    }

    public function getDurationWithoutSpacesAttribute()
    {
        return str_replace(' mins', 'mins', str_replace(' hr', 'hr', $this->duration));
    }

    public function timesheet()
    {
        return $this->hasOne(Timesheet::class, 'job_id');
    }
    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function to_language()
    {
        return $this->hasOne(Language::class, 'id', 'to_language_id');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'job_id');
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'documentable');
    }

    // public function isWithin48Hours(): bool
    // {
    //     $now = Carbon::now(); // Get the current date and time
    //     $appointmentDateTime = $this->appointment_date->setTimeFromTimeString($this->start_time); // Set the appointment time

    //     // Get the time 48 hours ago
    //     $twoDaysAgo = $now->copy()->subHours(48);

    //     // Check if the appointment time is within the past 48 hours from now
    //     return $appointmentDateTime->greaterThan($twoDaysAgo) && $appointmentDateTime->lessThanOrEqualTo($now);
    // }



    public function isWithin48Hours(): bool
    {
        // Get the current date and time in the Europe/London timezone
        $now = Carbon::now('Europe/London');

        // Set the appointment time in the Europe/London timezone
        $appointmentDateTime = $this->appointment_date
            ->setTimezone('Europe/London')
            ->setTimeFromTimeString($this->start_time);

        // Get the time 48 hours ago in the Europe/London timezone
        $twoDaysAgo = $now->copy()->subHours(48);

        // Check if the appointment time is within the past 48 hours from now
        return $appointmentDateTime->greaterThan($twoDaysAgo) && $appointmentDateTime->lessThanOrEqualTo($now);
    }


}

?>