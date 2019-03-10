> Lesson-4 A User May Respond to Threads.
===
### 1) create reply.blade.php
---
```
<div class="card">
    <div class="card-header">
        <i><a href="#">{{ $reply->owner->name }} </a></i>said {{ $reply->created_at->diffForHumans() }}...
    </div>
    <div class="card-body">{{ $reply->body }}</div>
</div>
```
### 2) edit show.blade.php 
---
```
@foreach($thread->replies as $reply)
    @include('threads/reply')
@endforeach
```
### 3) create new ThreadTest for unit folder
---
`php artisan make:test ThreadTest --unit`
```
<?php
namespace Tests\Unit;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
class ThreadTest extends TestCase
{
    use DatabaseMigrations;
    function a_thread_has_a_creator()
    {
        $thread = factory('App\Thread')->create();
        $this->assertInstanceOf('App\User', $thread->creator);
    }
    function a_thread_has_replies()
    {
        $thread = factory('App\Thread')->create();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $thread->replies);
    }
}
```
### 4)edit Thread model
---
```
use App\User;
...
public function creator()
{
    return $this->belongsTo(User::class, 'user_id');
}
```
### 5) edit threads/show.blade.php
---
```
...
<div class="card-header">
    <a href="#">
        {{ $thread->creator->name }}
    </a> posted: {{ $thread->title }}
</div>
...
```
### 6)create new PraticipateInForum test file
`php artisan make:test PraticipateInForum`
### 7)edit test/Feature/PraticipateInForumTest.php
---
```
<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    function unauthenticated_users_may_not_add_replies()
    {
        $this->expectException('Illuminate\Auth\AuthenticationException');
        $this->post('/threads/1/replies', []);
    }
    /** @test */
    function an_authenticated_user_may_participate_in_forum_threads()
    {
        $this->be($user = factory('App\User')->create());
        $thread = factory('App\Thread')->create();
        $reply = factory('App\Reply')->make();
        $this->post('/threads/'.$thread->id.'/replies',$reply->toArray());
        $this->get($thread->path())
            ->assertSee($reply->body);
    }
}
```
# 7)edit routes/web.php
---
```
Route::post('/threads/{thread}/replies', 'RepliesController@store');
```
### 8)edit contollers/RepliesController.php
---
```
use App\Thread;
public function __construct(){
    $this->middleware('auth');
}
public function store(Thread $thread){
    $thread->addReply([
        'body' => request('body'),
        'user_id' => auth()->id()
    ]);
    return back();
}
```
### 9) edit tests/Unit/ThreadTest.php
---
```
/** @test */
public function a_thread_can_add_a_reply(){
    $thread = factory('App\Thread')->create();
    $thread->addReply([
        'body' => 'Foobar',
        'user_id' => 1
    ]);
    $this->assertCount(1, $thread->replies);
}
```
### 10) edit app/Thread.php
---
```
protected $guarded = [];
public function addReply($reply){
    $this->replies()->create($reply);
}
```
### 10) edit app/Reply.php
---
```
protected $guarded = [];
```

> Note:
running single test `phpunit --filter method_name`
```
phpunit --filter a_thread_has_replies
```