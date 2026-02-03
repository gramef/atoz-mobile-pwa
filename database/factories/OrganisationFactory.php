<?php

use Faker\Generator as Faker;

$factory->define(App\Organisation::class, function (Faker $faker) {
    return [
        'organisation_company' => $faker->name,
        'vat_number' => $faker->name,
        'company_number' => $faker->phoneNumber,
        'organisation_address_line_1' => $faker->streetAddress,
        'organisation_address_line_2' => $faker->streetAddress,
        'organisation_county' => $faker->city,
        'organisation_postcode' => $faker->postcode,
        'organisation_email' => $faker->email
    ];
});
