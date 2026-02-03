<?php

namespace App\Observers;

use App\Mail\JobMail;
use App\TranslatorJob;
use App\Jobs\MatchAgents;
use Illuminate\Support\Facades\Mail;

class TranslatorJobObserver
{
    public function created(TranslatorJob $translatorJob)
    {
        Mail::to(config('app.to.address'))->send(new JobMail(
            $translatorJob,
            'emails.translator-jobs.created',
            'Job Request',
            'admin'
        ));

        Mail::to($translatorJob->client->user)->send(new JobMail(
            $translatorJob,
            'emails.translator-jobs.created',
            'Job Request Confirmation',
            'client'
        ));

        MatchAgents::dispatch($translatorJob, null, $translatorJob->requested_agent_id);
    }

    public function updated(TranslatorJob $translatorJob)
    {
        if ($translatorJob->isDirty('status') && $translatorJob->statusName === 'pending') {
            return;
        }

        if ($translatorJob->isDirty('status') && $translatorJob->statusName === 'rejected') {

            Mail::to(config('app.to.address'))->send(new JobMail(
                $translatorJob,
                'emails.agents.quote-rejected',
                'Quote was not successful',
                'admin'
            ));

            Mail::to($translatorJob->agent->user)->send(new JobMail(
                $translatorJob,
                'emails.agents.quote-rejected',
                'Quote was not successful',
                'agent'
            ));

            return;
        }

        if ($translatorJob->isDirty('status')) {

            Mail::to(config('app.to.address'))->send(new JobMail(
                $translatorJob,
                'emails.translator-jobs.status-update',
                'Job ' . ucfirst($translatorJob->statusName),
                'admin'
            ));

            Mail::to($translatorJob->client->user)->send(new JobMail(
                $translatorJob,
                'emails.translator-jobs.status-update',
                'Job ' . ucfirst($translatorJob->statusName),
                'client'
            ));

            if ($translatorJob->agent) {
                Mail::to($translatorJob->agent->user)->send(new JobMail(
                    $translatorJob,
                    'emails.translator-jobs.status-update',
                    'Job ' . ucfirst($translatorJob->statusName),
                    'agent'
                ));
            }

            return;
        }

        if (auth()->user()->hasRole(['admin', 'client'])) {

            $original = new TranslatorJob($translatorJob->getOriginal());

            Mail::to(config('app.to.address'))->send(new JobMail(
                [
                    'updated' => $translatorJob,
                    'original' => $original,
                ],
                'emails.translator-jobs.updated',
                'Job Updated',
                'admin'
            ));

            Mail::to($translatorJob->client->user)->send(new JobMail(
                [
                    'updated' => $translatorJob,
                    'original' => $original,
                ],
                'emails.translator-jobs.updated',
                'Job Updated',
                'client'
            ));

            if ($translatorJob->agent_id) {
                Mail::to($translatorJob->agent->user)->send(new JobMail(
                    [
                        'updated' => $translatorJob,
                        'original' => $original,
                    ],
                    'emails.agents.translator-job-updated',
                    'Job Updated',
                    'agent'
                ));
            }

            return;
        }
    }
}
