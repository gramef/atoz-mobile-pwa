<?php

namespace App\Mail;

use App\AdminQuote;
use App\ContactMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AcceptedQuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;
    public $job;
    public $view;
    public $subject;
    public $role;
    public $wasSentToAdmin;
    public $jobLink;

    public function __construct(AdminQuote $adminQuote, $view, $subject, $role = null)
    {
        $job =  $adminQuote->job;
        $this->quote = $adminQuote;
        
        $this->job = $adminQuote->job;
        $this->view = $view;
        $this->subject = $subject;
        $this->role = $role;
        
        $this->newContactMethods = request()->filled('contact_method') ? ContactMethod::whereIn('id', request()->contact_method)->get() : collect();


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

        return $this->withSwiftMessage(function ($message){
            $message->getHeaders()
                ->addTextHeader('x-mailgun-native-send', true);
        })->subject($this->subject)->view($this->view);
    }
}
