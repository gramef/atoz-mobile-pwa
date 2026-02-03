<?php

use App\TranslatorJob;
use Faker\Generator as Faker;

$factory->define(TranslatorJob::class, function (Faker $faker) {
    TranslatorJob::flushEventListeners();

    $status = $faker->numberBetween(0, 4);

    return [
        'from_language_id' => $faker->numberBetween(1,3),
        'to_language_id' => $faker->numberBetween(1,3),
        'skill_id' => $faker->numberBetween(7, 9),
        'status' => $status,
        'word_count' => $faker->numberBetween(0, 10000),
        'notes' => $faker->text,
        'client_reference' => $faker->name,
        'target_date' => $faker->dateTimeBetween('+1 week', '+1 month'),
        'affirmation' => $faker->boolean,
        'affidavit' => $faker->boolean,
        'cancelled_at' => $status === 2 ? today() : null,
    ];
});
