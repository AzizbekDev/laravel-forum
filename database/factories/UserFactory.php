<?php

use Illuminate\Support\Str;
use Faker\Generator as Faker;
$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});
$factory->define(App\Thread::class, function($faker){
    return[
        'user_id' => function(){
            return factory('App\User')->create()->id;
        },
        'channel_id' =>function(){
            return factory('App\Channel')->create()->id;
        },
        'title' => $faker->sentence,
        'body'=> $faker->paragraph
    ];
});

$factory->define(App\Channel::class, function($faker){
    $name = $faker->word;
    return [
        'name' => $name,
        'slug' => $name,
    ];
});

$factory->define(App\Reply::class, function($faker){
    return [
        'thread_id' => function(){
            return factory('App\Thread')->create()->id;
        },
        'user_id' => function(){
            return factory('App\User')->create()->id;
        },
        'body' => $faker->paragraph
    ];
});