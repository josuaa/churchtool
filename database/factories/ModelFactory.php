<?php

use app\Capability as C;

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

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Group::class, function () {
    static $i = 0;

    $keys = [
        'name',
        'member_capabilities',
        'admin_capabilities',
        'enable_mail'
    ];
    $values = [
        ['Mitglieder', C::VIEW_USER_ADDRESS_DATA, 0b0, false],
        ['Gemeindeleitung', C::MANAGE_USERS, 0b0, false]
    ];

    return array_combine($keys, $values[$i++]);
});
