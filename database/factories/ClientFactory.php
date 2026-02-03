<?php

use App\Client;
use Faker\Generator as Faker;

$factory->define(Client::class, function (Faker $faker) {
    Client::flushEventListeners();
    
    return [
        'contact_number' => $faker->phoneNumber,
        'client_address_line_1' => $faker->streetAddress,
        'client_address_line_2' => $faker->streetAddress,
        'client_county' => $faker->city,
        'client_postcode' => $faker->postcode,
        'seen_terms' => $faker->boolean
    ];
});
