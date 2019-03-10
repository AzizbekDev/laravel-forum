> Lesson-5 The Reply Forum.
===
### 2) edit show.blade.php
---
```
@if (auth()->check())
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="POST" action="{{ $thread->path() . '/replies' }}">
                {{ csrf_field() }}
                <div class="form-group">
                    <textarea name="body" id="body" class="form-control" placeholder="Have something to say?" rows="5"></textarea>
                </div>
                <button type="submit" class="btn btn-default">Post</button>
            </form>
        </div>
    </div>
@else
    <p class="text-center">Please <a href="{{ route('login') }}">sign in</a> to participate in this discussion.</p>
@endif
```
### 2) edit app.blade.php // adding nav links
---
```
<ul class="navbar-nav mr-auto">
    <li class="nav-item active">
        <a class="nav-link" href="/threads">All threads <span class="sr-only">(current)</span></a>
    </li>
</ul>
```