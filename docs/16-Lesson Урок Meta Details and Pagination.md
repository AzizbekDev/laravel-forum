16-Lesson Урок Meta Details and Pagination
===
### 1) edit `resources/views/threads/show.blade.php`
---
```
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                        <a href="#">
                            {{ $thread->creator->name }}
                    </a> posted: {{ $thread->title }}
                </div>
                <div class="panel-body">
                    {{ $thread->body }}
                </div>
            </div>
            @foreach ($replies as $reply)
                @include('threads.reply')
            @endforeach
            {{ $replies->links() }}

            @if (auth()->check())
                <form method="POST" action="{{ $thread->path() . '/replies' }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <textarea name="body" id="body" class="form-control" placeholder="Have something to say?" rows="5"></textarea>
                    </div>
                    <button type="submit" class="btn btn-default">Post</button>
                </form>
            @else
                <p class="text-center">Please <a href="{{ route('login') }}">sign in</a> to participate in this discussion.</p>
            @endif
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-body">
                    This thread was published {{ $thread->created_at->diffForHumans() }}
                    by <a href="#">{{ $thread->creator->name }}</a>, and currently has {{ $thread->replies_count }}
                    {{ str_plural('comment', $thread->replies_count)}}.
                </div>
            </div>
        </div>
    </div>
</div>
```
### 2) edit `app/Thread.php`
---
```
protected static function boot()
{
    parent::boot();
    static::addGlobalScope('replyCount', function($builder){
        $builder->withCount('replies');
    });
}
```
### 3) edit `app/Http/Controllers/ThreadsController.php`
---
```
public function show($channelId, Thread $thread)
{
    return view('threads.show', [
        'thread' => $thread,
        'replies' => $thread->replies()->paginate(10)
    ]);
}
```