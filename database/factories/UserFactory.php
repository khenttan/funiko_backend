<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Str;
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
    return [
        'firstname'         => $faker->firstName,
        'lastname'          => $faker->lastName,
        'cellphone'         => $faker->phoneNumber,
        'email'             => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token'    => Str::random(10),
    ];
});

$factory->state(User::class, 'admin', [

]);

// sync admin roles to user
$factory->afterCreatingState(User::class, 'admin', function ($user, $faker) {
    $user->syncRoles([
        Role::$USER,
        Role::$ADMIN,
        Role::$ADMIN_NOTIFY,
        Role::$DEVELOPER
    ]);
});
