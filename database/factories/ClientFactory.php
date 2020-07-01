<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Client;
use App\Timezone;
use Faker\Generator as Faker;

$factory->define(Client::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'tz_id' => Timezone::all()->random()->id,
    ];
});
