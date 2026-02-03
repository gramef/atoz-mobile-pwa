<?php

namespace App\Mail\Admin;

use App\InterpreterJob;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InterpreterJobCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $interpreterJob;

    public function __construct(InterpreterJob $interpreterJob)
    {
        $this->interpreterJob = $interpreterJob;
    }

    public function build()
    {
        return $this
            ->subject('Job Request')
            ->view('emails.admin.interpreter-job-created')
            ->withSwiftMessage(function ($message){
                $message->getHeaders()
                        ->addTextHeader('x-mailgun-native-send', true);
            })
    }
}
