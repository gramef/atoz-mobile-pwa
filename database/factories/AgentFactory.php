<?php

use App\Agent;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Agent::class, function (Faker $faker) {
    Agent::flushEventListeners();

    return [
        'user_id' => function () use ($faker) {
            $roles = ['community-interpreter', 'qualified-interpreter'];
            
            $user = factory(App\User::class)->create()
                ->assignRole(['agent', $roles[$faker->boolean]]);

            return $user->id;
        },
        'address_line_1' => $faker->streetAddress,
        'address_line_2' => $faker->streetAddress,
        'county' => $faker->city,
        'postcode' => $faker->postcode,
        'contact_number' => $faker->phoneNumber,
        'gender' => $faker->boolean,
        'can_provide_affidavit' => $faker->boolean,
        'can_provide_affirmation' => $faker->boolean,
        'profile_picture' => '/img/profile-picture.jpg',
        'dbs_expiry_date' => Carbon::now()->addDays(rand(1, 100))->format('Y-m-d'),
        'induction_date' => Carbon::now()->addDays(rand(1, 100))->format('Y-m-d'),
        'restrict_job_notifications' => $faker->boolean,
        'latitude' => $faker->latitude,
        'longitude' => $faker->longitude,
    ];
});
