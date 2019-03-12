11-Lesson Users Can Filter Threads By Channel
===
### 1) edit `tests/Feature/ReadThreadsTest.php`
---
```
public function a_user_can_filter_according_to_a_channel()
{
    $channel = create('App\Channel');
    $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
    $threadNotChannel = create('App\Thread');
    $this->get('/threads/'. $channel->slug)
        ->assertSee($threadInChannel->title)
        ->assertDontSee($threadNotChannel->title);
}
```

### 2) edit `routes/web.php`
---
```
Route::get('threads/{channel}', 'ThreadController@index');
```
### 3) edit `app/Http/Controllers/ThreadsController.php`
---
```
use App\Channel;
...
public function index(Channel $channel)
{
    if($channel->exists){
        $threads = $channel->threads()->latest()->get();
    }else{
        $threads = Thread::latest()->get();
    }
    return view('threads.index',compact('threads'));
}
```
### 4) edit `app/Channel.php`
---
```
public function getRouteKeyName()
{
    return 'slug';
}
public function threads()
{
    return $this->hasMany(Thread::class);
}
```
### 5) create and edit `tests/Unit/ChannelTest.php`
---
```
<?php
namespace Tests\Unit;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
class ChannelTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    public function a_channel_consists_of_threads()
    {
        $channel = create('App\Channel');
        $thread = create('App\Thread', ['channel_id' => $channel->id]);
        $this->assertTrue($channel->contains($thread));
    }
}
```
### 6) create and edit `tests/Unit/ChannelTest.php`
---
```

```