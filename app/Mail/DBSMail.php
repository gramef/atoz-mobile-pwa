<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DBSMail extends Mailable
{
    use Queueable, SerializesModels;

    public $agent;
    public $view;
    public $subject;

    public function __construct($agent, $view, $subject)
    {
        $this->agent = $agent;
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
