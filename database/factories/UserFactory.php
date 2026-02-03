<?php

use App\User;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    User::flushEventListeners();

    return [
        'title' => $faker->numberBetween(0,5),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => "$2y$12$7m2vciQKDaJ1JXKM6hZgDu4pqq/Hs3dA.LyE/boVRLhwG/wizKNf6", // secret
        'remember_token' => str_random(10),
        'enabled' => 1,
    ];
});
