<?php

use Carbon\Carbon;
use App\InterpreterJob;
use Faker\Generator as Faker;

$factory->define(InterpreterJob::class, function (Faker $faker) {
    InterpreterJob::flushEventListeners();

    $status = $faker->numberBetween(0, 4);

    return [
        'skill_id' => $faker->numberBetween(1, 6),
        'require_qualified' => $faker->boolean,
        'from_language_id' => $faker->numberBetween(1,3),
        'to_language_id' => $faker->numberBetween(1,3),
        'status' => $status,
        'gender' => $faker->numberBetween(0, 2),
        'special_requirements' => $faker->text,
        'contact_information' => $faker->text,
        'contact_person_details' => $faker->text,
        'personal_identity_number' => $faker->name,
        'user_title' => $faker->numberBetween(0,5),
        'user_first_name' => $faker->firstName,
        'user_last_name' => $faker->lastName,
        'appointment_date' => Carbon::now()->addDays(rand(1, 100))->format('Y-m-d'),
        'department' => $faker->company,
        'address_line_1' => $faker->streetAddress,
        'address_line_2' => $faker->streetAddress,
        'county' => $faker->city,
        'client_reference' => $faker->name,
        'postcode' => $faker->postcode,
        'start_time' => $faker->time('H:i'),
        'end_time' => $faker->time('H:i'),
        'duration_hours' => $faker->numberBetween(0, 3),
        'duration_minutes' => $faker->numberBetween(0, 59),
        'file_reference' => 'some reference',
        "date_of_birth" => Carbon::now()->addDays(rand(1, 100))->format('Y-m-d'),
        'cancelled_at' => $status === 2 ? today() : null,
    ];
});
