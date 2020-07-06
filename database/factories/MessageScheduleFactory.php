<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\MessageSchedule;
use App\Message;
use Faker\Generator as Faker;

$factory->define(MessageSchedule::class, function (Faker $faker) {
    return [
        'message_id' => Message::all()->random()->id,
        'time' => str_pad(rand(0,23), 2, '0', STR_PAD_LEFT)
                . ':' . str_pad(rand(0,59), 2, '0', STR_PAD_LEFT),
    ];
});
