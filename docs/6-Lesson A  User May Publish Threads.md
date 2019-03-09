/*** > A user may publish threads ***/
1) create new test file CreateThreadsTest.php

#~ php artisan make:test CreateThreadsTest

2) edit tests/Feature/CreateThreadsTest.php
/*** *****************************************************/
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
        $this->expectException('Illuminate\Auth\AuthenticationException');
        
        $thread = factory('App\Thread')->make();

        $this->post('/threads', $thread->toArray());
    }
    
    /** @test */
    public function an_authenticated_user_can_create_new_forum_threads()
    {
        $this->actingAs(factory('App\User')->create());
        
        $thread = factory('App\Thread')->make();

        $this->post('/threads', $thread->toArray());

        $this->get($thread->path())
            ->assertSee($thread->body)
            ->assertSee($thread->title);
    }
}
/***************************************************** ***/

3) edit routes/web.php
/*** *****************************************************/
    Route::post('/threads', 'ThreadsController@store');

/***************************************************** ***/

4) edit controller ThreadsController.php
/*** *****************************************************/
public function __construct()
{
    $this->middleware('auth')->only('store');
}
...
public function store(Request $request)
{
    $thread = Thread::create([
        'user_id' => auth()->id(),
        'title' => $request->title,
        'body' => $request->body
    ]);
    return redirect($thread->path());
}

/***************************************************** ***/