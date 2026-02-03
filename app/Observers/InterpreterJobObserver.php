<?php

namespace App\Observers;

use App\Agent;
use Carbon\Carbon;
use App\Mail\JobMail;
use GuzzleHttp\Client;
use App\InterpreterJob;
use App\Jobs\MatchAgents;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InterpreterJobObserver
{
    public function created(InterpreterJob $interpreterJob)
    {
		
		try {
			Mail::to(config('app.to.address'))->send(new JobMail(
			    $interpreterJob,
			    'emails.interpreter-jobs.created',
			    'Job Request',
			    'admin'
			));
			
			Mail::to($interpreterJob->client->user)->send(new JobMail(
			    $interpreterJob,
			    'emails.interpreter-jobs.created',
			    'Job Request Confirmation',
			    'client'
			));
		} catch (\Exception $e) {
			Log::info( $e->getMessage() );
		} catch (\Throwable $e) {
			Log::info( $e->getMessage() );
		}       

        MatchAgents::dispatch($interpreterJob, null, $interpreterJob->requested_agent_id);
    }

    public function saving(InterpreterJob $interpreterJob)
    {
        if ($interpreterJob->isDirty(['start_time', 'duration_hours', 'duration_minutes'])) {
            $interpreterJob->end_time = Carbon::parse($interpreterJob->start_time)
                ->addHours($interpreterJob->duration_hours)
                ->addMinutes($interpreterJob->duration_minutes);
        }

        if (!$interpreterJob->hasAddressFields()) {
            return;
        }
        /*
                if ($interpreterJob->isDirty('postcode') && isset($interpreterJob->postcode)) {
                    $location = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($interpreterJob->postcode) . '&key=AIzaSyDKypyV_to1UeVcCmygrW9UIa_VVHGHFXU'), true);

                    if (!empty($location['results'])) {
                        $interpreterJob->longitude = $location['results'][0]['geometry']['location']['lng'];
                        $interpreterJob->latitude = $location['results'][0]['geometry']['location']['lat'];
                    } else {
                        $location = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($interpreterJob->address_line_1) . ',' . urlencode($interpreterJob->address_line_2) . ',' . urlencode($interpreterJob->county) . '&key=AIzaSyDKypyV_to1UeVcCmygrW9UIa_VVHGHFXU'), true);

                        if (!empty($location['results'])) {
                            $interpreterJob->longitude = $location['results'][0]['geometry']['location']['lng'];
                            $interpreterJob->latitude = $location['results'][0]['geometry']['location']['lat'];
                        }
                    }
                }
                    */
        //Added by Sanmi Amos to fix the issue of the longitude and latitude not being updated by removing google api call and using postcodes.io api(for UK alone)
        $postcode = $interpreterJob->postcode;
        if ($interpreterJob->isDirty('postcode') && isset($interpreterJob->postcode)) {
            $client = new Client();
            $url = "https://api.postcodes.io/postcodes/{$postcode}";

            try {
                $response = $client->request('GET', $url);
                $data = json_decode($response->getBody()->getContents(), true);

                if (isset($data['result'])) {
                    $interpreterJob->longitude = $data['result']['longitude'];
                    $interpreterJob->latitude = $data['result']['latitude'];
                }
            } catch (\Exception $e) {
                return null;
            }

        }
    }

    public function updated(InterpreterJob $interpreterJob)
    {
        Log::info('Interpreter Job Updated');
        if ($interpreterJob->isDirty('status') && $interpreterJob->statusName === 'pending') {

            $original = new InterpreterJob($interpreterJob->getOriginal());           
			
			try {
				
				Mail::to(config('app.to.address'))->send(new JobMail(
				    [
				        'updated' => $interpreterJob,
				        'original' => $original,
				    ],
				    'emails.interpreter-jobs.updated',
				    'Job Updated',
				    'admin'
				));
			
			} catch (\Exception $e) {
				Log::info( $e->getMessage() );
			} catch (\Throwable $e) {
				Log::info( $e->getMessage() );
			}

            return;
        }

        if ($interpreterJob->agent_id) {
            if (!$interpreterJob->agent) {
                $interpreterJob->load('agent');
            }
        }

        if ($interpreterJob->isDirty('status') && $interpreterJob->statusName === 'rejected') {
     			
			try {
				
				Mail::to(config('app.to.address'))->send(new JobMail(
				    $interpreterJob,
				    'emails.agents.quote-rejected',
				    'Quoted not successful',
				    'admin'
				));
				
				Mail::to($interpreterJob->agent->user)->send(new JobMail(
				    $interpreterJob,
				    'emails.agents.quote-rejected',
				    'Quoted not successful',
				    'agent'
				));
			
			} catch (\Exception $e) {
				Log::info( $e->getMessage() );
			} catch (\Throwable $e) {
				Log::info( $e->getMessage() );
			}

            return;
        }

        if ($interpreterJob->isDirty('status')) {

            if ($interpreterJob->statusName !== 'completed') { 				
				try {
				
					Mail::to(config('app.to.address'))->send(new JobMail(
					    $interpreterJob,
					    'emails.interpreter-jobs.status-update',
					    'Job ' . ucfirst($interpreterJob->statusName),
					    'admin'
					));
				
				} catch (\Exception $e) {
					Log::info( $e->getMessage() );
				} catch (\Throwable $e) {
					Log::info( $e->getMessage() );
				}
            }
			
			try {
			
				Mail::to($interpreterJob->client->user)->send(new JobMail(
				    $interpreterJob,
				    'emails.interpreter-jobs.status-update',
				    'Job ' . ucfirst($interpreterJob->statusName),
				    'client'
				));
			
			} catch (\Exception $e) {
				Log::info( $e->getMessage() );
			} catch (\Throwable $e) {
				Log::info( $e->getMessage() );
			}

           

            if ($interpreterJob->agent_id) {                
				
				try {
				
					Mail::to($interpreterJob->agent->user)->send(new JobMail(
					    $interpreterJob,
					    'emails.interpreter-jobs.status-update',
					    'Job ' . ucfirst($interpreterJob->statusName),
					    'agent'
					));
				
				} catch (\Exception $e) {
					Log::info( $e->getMessage() );
				} catch (\Throwable $e) {
					Log::info( $e->getMessage() );
				}
				
				
            } elseif ($interpreterJob->statusName === 'cancelled') {
                $agent = Agent::find($interpreterJob->getOriginal('agent_id'));
                if ($agent) {					
					
					try {
					
						Mail::to($agent->user)->send(new JobMail(
						    $interpreterJob,
						    'emails.interpreter-jobs.status-update',
						    'Job ' . ucfirst($interpreterJob->statusName),
						    'agent'
						));
					
					} catch (\Exception $e) {
						Log::info( $e->getMessage() );
					} catch (\Throwable $e) {
						Log::info( $e->getMessage() );
					}
					
                }
            }

            return;
        }

        if (auth()->user()->hasRole(['admin', 'client'])) {

            $original = new InterpreterJob($interpreterJob->getOriginal());        
			
			try {
			
				Mail::to(config('app.to.address'))->send(new JobMail(
				    [
				        'updated' => $interpreterJob,
				        'original' => $original,
				    ],
				    'emails.interpreter-jobs.updated',
				    'Job Updated',
				    'admin'
				));
				
				Mail::to($interpreterJob->client->user)->send(new JobMail(
				    [
				        'updated' => $interpreterJob,
				        'original' => $original,
				    ],
				    'emails.interpreter-jobs.updated',
				    'Job Updated',
				    'client'
				));
			
			} catch (\Exception $e) {
				Log::info( $e->getMessage() );
			} catch (\Throwable $e) {
				Log::info( $e->getMessage() );
			}

            if ($interpreterJob->agent_id) {               
				
				try {
			
					Mail::to($interpreterJob->agent->user)->send(new JobMail(
					    [
					        'updated' => $interpreterJob,
					        'original' => $original,
					    ],
					    'emails.agents.interpreter-job-updated',
					    'Job Updated',
					    'agent'
					));
				
				} catch (\Exception $e) {
					Log::info( $e->getMessage() );
				} catch (\Throwable $e) {
					Log::info( $e->getMessage() );
				}
				
            }

            return;
        }
    }
}