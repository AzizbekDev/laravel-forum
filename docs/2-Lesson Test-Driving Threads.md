***< Test-Driving Threads.***
`create test file using artisan commands`
#~ php artisan make:test ThreadsTest
`Edit test files` <tests/Features/ThreadsTest.php>
/*** *****************************************************/
<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
class ThreadsTest extends TestCase
{
    use DatabaseMigrations;
    public function a_user_can_view_all_threads()
    {
        $thread = factory('App\Thread')->create();
        $this->get('/threads')
        ->assertSee($thread->title);
    }
    function a_user_can_read_a_single_thread()
    {
        $thread = factory('App\Thread')->create();
        $this->get($thread->path())
        ->assertSee($thread->title);
    }
/***************************************************** ***/

`Edit phpunit file` <phpunit.xml>
/*** *****************************************************/
<php>
...
<server name="DB_CONNECTION" value="sqlite"/>
<server name="DB_DATABASE" value=":memory:"/>
...
</php>
/***************************************************** ***/
<notes:> after setup phpunit configurations we have installed `sqlite` && `phpunit`
for ubuntu cli commands
#~ sudo apt install phpunit
#~ sudo apt-get install php7.2-sqlite

`Edit routes` <routes/web.php>
/*** *****************************************************/
Route::get('/threads', 'ThreadsController@index');
Route::get('/threads/{thread}', 'ThreadsController@show');
/***************************************************** ***/

`Edit controller` <app/Http/Controllers/ThreadsController.php>
/*** *****************************************************/
public function index()
{
    $threads = Thread::latest()->get();
    return view('threads.index',compact('threads'));
}

public function show(Thread $thread)
{
    return view('threads.show', compact('thread'));
}
/***************************************************** ***/

`Run auth command for creating layouts folders`
#~ php artisan make:auth

`Create views threads.index` <resources/views/threads/index.blade.php>
/*** *****************************************************/
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">From Threads</div>
                <div class="card-body">
                    @foreach($threads as $thread)
                        <article>
                            <a href="{{ $thread->path() }}"><h4>{{ $thread->title }}</h4></a>
                            <div class="body"><p>{{ $thread->body }}</p></div>
                        </article><hr>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

/***************************************************** ***/

`Create views threads.show` <resources/views/threads/show.blade.php>
/*** *****************************************************/
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Single thread</div>
                <div class="card-body">
                    <article>
                        <h4>{{ $thread->title }}</h4>
                        <div class="body"><p>{{ $thread->body }}</p></div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

/***************************************************** ***/


`Edit model Thread` <app/Thread.php>
/*** *****************************************************/
public function path(){
    return '/threads/'.$this->id;
}
/***************************************************** ***/