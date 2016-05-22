<?php

use App\Models;
use Faker\Generator;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(Models\User::class, function (Generator $fake) {
    return [
        'name' => $fake->name,
        'email' => $fake->safeEmail,
        'password' => str_random(10),
        'remember_token' => str_random(10),
    ];
});
