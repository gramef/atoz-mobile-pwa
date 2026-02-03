<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $mail;

    public function __construct(User $user, Mailable $mail)
    {
        $this->user = $user;
        $this->mail = $mail;
    }

    public function handle()
    {
		try {
			Mail::to($this->user)->send($this->mail);
		} catch (\Exception $e) {
			Log::info( $e->getMessage() );
		} catch (\Throwable $e) {
			Log::info( $e->getMessage() );
		}
        
    }
}
