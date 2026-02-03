<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $job;
    public $view;
    public $subject;
    public $role;
    public $wasSentToAdmin;
    public $jobLink;

    public function __construct($job, $view, $subject, $role = null, $adminQuote = null)
    {
        $this->job = $job;
        $this->view = $view;
        $this->subject = $subject;
        $this->role = $role;

        if (is_array($job)) {
            $jobType = get_class($job['original']) === 'App\InterpreterJob' ? 'interpreter-jobs' : 'translator-jobs';
        } else {
            $jobType = get_class($job) === 'App\InterpreterJob' ? 'interpreter-jobs' : 'translator-jobs';
        }

        $this->jobLink = $role === 'agent' ?
            route("$jobType.show", $job) :
            route("$jobType.edit", $job);
    }

    public function build()
    {
        $this->wasSentToAdmin = $this->role === 'admin';

        return $this->withSwiftMessage(function ($message) {
            $message->getHeaders()
                ->addTextHeader('x-mailgun-native-send', true);
        })->subject($this->subject)->view($this->view);
    }
}