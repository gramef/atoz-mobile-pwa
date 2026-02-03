<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class JobReminder extends Notification
{
    use Queueable;


    public function __construct()
    {
        //
    }


    public function via($notifiable)
    {
        return ['mail'];
    }


    public function toMail($notifiable)
    {
        return (new MailMessage)
                     ->subject('Your Job Remainder')
            ->view('emails.agents.interpreter-job-reminder', ['job' => $notifiable->interpreter_job]);
    }


    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
