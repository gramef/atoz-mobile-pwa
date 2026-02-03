<?php

namespace App\Mail;

use App\ContactMethod;
use App\Quote;
use App\TranslatorJob;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $job;
    public $quote;
    public $subject;
    public $role;
    public $wasSentToAdmin;
    public $jobLink;


    public function __construct(TranslatorJob $translatorJob , $quote,  $view, $subject, $role = null)
    {
        $job = $translatorJob;
        $this->job = $translatorJob;
       
        $this->quote = $quote;
        $this->view = $view;
        $this->role = $role;
        $this->subject = $subject;
        $this->newContactMethods = request()->filled('contact_method') ? ContactMethod::whereIn('id', request()->contact_method)->get() : collect();

        if (is_array($job)) {
            $jobType = get_class($job['original']) === 'App\InterpreterJob' ? 'interpreter-jobs' : 'translator-jobs';
        } else {
            $jobType = get_class($job) === 'App\InterpreterJob' ? 'interpreter-jobs' : 'translator-jobs';
            
        }

        $this->jobLink = $role === 'admin' ?
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
