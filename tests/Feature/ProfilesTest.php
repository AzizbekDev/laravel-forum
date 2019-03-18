<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfilesTest extends TestCase
{
    use DatabaseMigrations;
   /** @test */
    public function a_user_has_a_profile()
    {
        $user = create('App\User');
        $this->get("/profile/{$user->name}")
        ->assertSee($user->name);
    }

    /** @test */
    function profiles_dispaly_all_threads_created_by_the_associated_user()
    {
        $user = create('App\User');
        $threads = create('App\Thread', ['user_id' => $user->id]);
        
        $this->get("/profile/{$user->name}")
        ->assertSee($threads->title)
        ->assertSee($threads->body);
    }
}