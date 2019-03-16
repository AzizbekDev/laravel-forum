17-Lesson A User Can Filter Threads By Popularity
===
### 1) edit `resources/views/threads/index.blade.php`
---
```
@foreach($threads as $thread)
<div class="panel panel-default">
    <div class="panel-heading">
        <a href="{{ $thread->path() }}">
            {{ $thread->title }}
        </a>
        <a href="{{ $thread->path() }}" class="pull-right">
            {{ $thread->replies_count }} {{ str_plural('reply', $thread->replies_count) }}
        </a>
    </div>
    <div class="panel-body">
        <div class="body"><p>{{ $thread->body }}</p></div>
    </div>
</div>
<hr>
@endforeach
```
### 2) edit `resources/views/layouts/app.blade.php`
---
```
... 
<style>
    body {
        padding-bottom: 100px;
    }
</style>
...
<ul class="dropdown-menu">
    @foreach ($channels as $channel)
        <li><a href="/threads/{{ $channel->slug }}">{{ $channel->name }}</a></li>
    @endforeach
    <li><a href="/threads?popular=1">Popular All Times</a></li> // add this link
</ul>
```
### 3) edit `tests/Feature/ReadThreadsTest.php`
---
```
function a_user_can_filter_threads_by_popularity()
{
    $threadWithTwoReplies = create("App\Thread");
    create('App\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);

    $threadWithThreeReplies = create("App\Thread");
    create('App\Reply', ['thread_id' => $threadWithThreeReplies->id], 3);

    $threadWithNoReplies = $this->thread;

    $response = $this->getJson('threads?popularity=1')->json();
    
    $this->assertEquals([3,2,0], array_column($response, 'replies_count'));

}
```
### 4) edit `tests/Utilities/functions.php`
---
```
function create($class, $attribute = [], $times = null)
{
    return factory($class, $times)->create($attribute);
}

function make($class, $attribute = [], $times = null)
{
    return factory($class,$times)->make($attribute);
}
```
### 5) edit `app/Http/Controllers/ThreadsController.php`
---
```
public function index(Channel $channel, ThreadFilters $filters)
{
    $threads = $this->getThreads($channel, $filters);
    // add this if operator
    if(request()->wantsJson()){
        return $threads;
    }
    return view('threads.index',compact('threads'));
}
```
### 6) edit `app/Filters/ThreadFilters.php`
---
```
protected function popular()
{
    $this->builder->getQuery()->orders = [];
    return $this->builder->orderBy('replies_count','desc');
}
```
