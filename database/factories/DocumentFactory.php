<?php

use Faker\Generator as Faker;
use Illuminate\Support\Facades\Storage;

$factory->define(App\Document::class, function (Faker $faker) {
    return [
        'url' => $faker->url,
        'name' => 'File-to-be-translated.doc'
    ];
});
