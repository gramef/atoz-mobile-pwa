<?php

namespace App\Observers;

use App\Agent;
use GuzzleHttp\Client;

class AgentObserver
{
    public function saving(Agent $agent)
    {
        /*
        if ($agent->isDirty('postcode')) {
            $location = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($agent->postcode) . '&key=AIzaSyDKypyV_to1UeVcCmygrW9UIa_VVHGHFXU'), true);

            if (!empty($location['results'])) {
                $agent->longitude = $location['results'][0]['geometry']['location']['lng'];
                $agent->latitude = $location['results'][0]['geometry']['location']['lat'];
            }
        }
*/

        //Added by Sanmi Amos to fix the issue of the longitude and latitude not being updated by removing google api call and using postcodes.io api(for UK alone)

        $postcode = $agent->postcode;
        if ($agent->isDirty('postcode')) {
            $client = new Client();
            $url = "https://api.postcodes.io/postcodes/{$postcode}";

            try {
                $response = $client->request('GET', $url);
                $data = json_decode($response->getBody()->getContents(), true);

                if (isset($data['result'])) {
                    $agent->longitude = $data['result']['longitude'];
                    $agent->latitude  = $data['result']['latitude'];
                }
            } catch (\Exception $e) {
                return null;
            }

        }

        if ($agent->isDirty('dbs_expiry_date')) {
            $agent->user->unreadNotifications()->update(['read_at' => now()]);
            $agent->notified_of_dbs = false;
            $agent->notified_week_of_dbs = false;
        }
    }
}
