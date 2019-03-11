Lesson-3 A Thread Can Have Replies
===
### 1) rename and edit ThreadTest file `tests/Feature/ThreadTest.php` => `ReadThreadTest.php`
---
```
public function setUp(){
    parent::setUp();
    $this->thread = factory('App\Thread')->create();
}
public function a_user_can_view_all_thread(){
    $response = $this->get('/threads')->assertSee($this->thread->title);
}

function a_user_can_read_a_single_thread(){
    $this->get('/threads/'.$this->thread->id)->assertSee($this->thread->title);
}

function a_user_can_read_replies_that_are_associated_with_a_thread(){
    $reply = factory('App\Reply')->create(['thread_id' => $this->thread->id]);

    $this->get('/threads/'.$this->thread->id)->assertSee($reply->body);
}
```
### 2)edit threads/show.blade.php
---
```
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $thread->title }}</div>
                <div class="card-body">{{ $thread->body }}</div>
            </div>
        </div>
    </div><hr>
    <div class="row justify-content-center">
        <div class="col-md-8">
        @foreach($thread->replies as $reply)
            <div class="card">
                <div class="card-header">
                    <i><a href="#">{{ $reply->owner->name }} </a></i>said {{ $reply->created_at->diffForHumans() }}...
                </div>
                <div class="card-body">{{ $reply->body }}</div>
            </div>
        @endforeach
        </div>
    </div>
</div>
@endsection
```
### 3)edit app/Threads.php
---
```
    use App\Reply;
    ...
    public function replies(){
        return $this->hasMany(Reply::class);
    }
```
### 4)edit app/Reply.php
---
```
use App\User;
    ...
public function owner(){
    return $this->belongsTo(User::class, 'user_id');
}
```
### 5) create UnitTest `php artisan make:test ReplyTest --unit` run via cli and edit test/Unit/ReplyTest.php
---
```

<?php
namespace Tests\Unit;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function is_has_an_owner(){
        $reply = factory('App\Reply')->create();
        $this->assertInstanceOf('App\User', $reply->owner);
    }
```