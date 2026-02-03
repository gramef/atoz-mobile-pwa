<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $user;
    public $view;
    public $subject;

    public function __construct($token, User $user, $view, $subject)
    {
        $this->token = $token;
        $this->user = $user;
        $this->view = $view;
        $this->subject = $subject;
    }

    public function build()
    {
        return $this->withSwiftMessage(function ($message){
            $message->getHeaders()
                ->addTextHeader('x-mailgun-native-send', true);
        })->subject($this->subject)->view($this->view);
    }
}
