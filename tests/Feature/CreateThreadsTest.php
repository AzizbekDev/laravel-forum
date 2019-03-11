<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_may_not_create_threads()
    {
        $this->withExceptionHandling();
        $this->get('/threads/create')
            ->assertRedirect('/login');
        $this->post('/threads', [])
            ->assertRedirect('/login');
    }
    
    /** @test */
    public function an_authenticated_user_can_create_new_forum_threads()
    {
        $this->signIn();
        $thread = make('App\Thread'); 
        $response = $this->post('/threads', $thread->toArray());
        $this->get($response->headers->get('Location'))
            ->assertSee($thread->body)
            ->assertSee($thread->title);
    }
    
    /** @test */
    public function a_thread_require_a_title()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    public function publishThread($overrides = [])
    {
        $this->withExceptionHandling()->signIn();
        $thread = make('App\Thread', $overrides);
        return $this->post('/threads', $thread->toArray());

    }
}
