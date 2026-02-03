<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DBSExpiring extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your DBS number is expiring')
            ->view('emails.agents.dbs-expiring', ['agent' => $notifiable->agent]);
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
