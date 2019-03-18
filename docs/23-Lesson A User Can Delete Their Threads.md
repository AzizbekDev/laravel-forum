23-Lesson A User Can Delete Their Threads
===
### 2) edit `routes/web.php`
---
```
Route::get('/profile/{user}', 'ProfilesController@show');
```
change to
```
Route::get('/profile/{user}', 'ProfilesController@show')->name('profile');
```
### 2) edit `resources/views/threads/show.blade.php`
---
```
 <a href="/profile/{{ $thread->creator->name }}">
 ...
```
change to
```
<a href="{{ route('profile', $thread->creator) }}">
```
### 3) edit `resources/views/threads/reply.blade.php`
---
```
<a href="/profile/{{ $reply->owner->name }}">
```
change to
```
<a href="{{ route('profile', $reply->owner) }}">
```
### 4) create and edit `resources/views/layouts/nav.blade.php`
---
```
<nav class="navbar navbar-default navbar-static-top">
    <div class="container">
        <div class="navbar-header">
            <!-- Collapsed Hamburger -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!-- Branding Image -->
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
        </div>
        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav">
                &nbsp;
                <li class="dropdown">
                    <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Browse <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="/threads">All threads <span class="sr-only">(current)</span></a></li>
                        @if(auth()->check())
                        <li><a class="nav-link" href="/threads?by={{ auth()->user()->name }}">My threads <span class="sr-only">(current)</span></a></li>
                        @endif
                        <li><a href="/threads?popular=1">Popular All Times</a></li>
                    </ul>
                </li>
                <li>
                    <a class="nav-link" href="/threads/create">New threads <span class="sr-only">(current)</span></a>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Channels <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        @foreach ($channels as $channel)
                            <li><a href="/threads/{{ $channel->slug }}">{{ $channel->name }}</a></li>
                        @endforeach
                    </ul>
                </li>
            </ul>
            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->
                @guest
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('register') }}">Register</a></li>
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('profile', Auth::user()) }}">My profile</a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
```
### 5) edit `resources/views/layouts/app.blade.php`
---
```
...
<body>
    <div id="app">
        @include('layouts.nav') // include nav file
        @yield('content')
    </div>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
...
```
### 5) edit `resources/views/proifles/show.blade.php`
---
```
@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="col-md-8 col-md-offset-2">
            <div class="page-header">
                <h1>
                    {{ $profileUser->name }}
                    <small>Since {{ $profileUser->created_at->diffForHumans() }}</small>
                </h1>
            </div>
            @foreach ($threads as $thread)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="#">{{ $thread->creator->name }}</a> posted: {{ $thread->title }}
                    <div class="pull-right">
                        {{ $thread->created_at->diffForHumans() }}
                    </div>
                </div>
                <div class="panel-body">
                    {{ $thread->body }}
                </div>
            </div>
            @endforeach
            {{ $threads->links() }}
        </div>
    </div>
@endsection
```
### 6) edit `tests/Feature/CreateThreadsTest.php`
---
```
function guests_cannot_delete_threads()
{
    $this->withExceptionHandling();
    $thread = create('App\Thread');
    $response = $this->delete($thread->path());
    $response->assertRedirect('/login');
}
function a_thread_can_be_deleted()
{
    $this->signIn();
    $thread = create('App\Thread');
    $reply = create('App\Reply', ['thread_id'=> $thread->id]);
    
    $response = $this->json('DELETE', $thread->path());

    $response->assertStatus(204);

    $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
    
    $this->assertDatabaseMissing('replies',['id' => $reply->id]);
}
```
### 7) edit `routes/web.php`
---
```
Route::delete('threads/{channel}/{thread}', 'ThreadsController@destroy'); //add this delete method
```
### 8) edit `app/Http/Controllers/ThreadsController.php`
---
```
 public function destroy($channel, Thread $thread)
{
    $thread->delete();
    if(request()->wantsJson()){
        return response([], 204);
    }
    return redirect('/threads');
}
```
### 9) edit `app/Thread.php`
---
```
protected static function boot()
    {
        ...
        // add this static method for autodeleteing related thread replies
        static::deleting(function($thread){
            $thread->replies()->delete();
        });
    }
```
### 10) edit `resources/views/threads/show.blade.php`
---
```
...
<div class="panel-heading">
    ...
    // add button for deleting thread
    <div class="pull-right">
        <form method="POST" action="{{ $thread->path() }}">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <button class="btn btn-danger btn-sm" type="submit">Delete</button>
        </form>
    </div>
</div>
...
```
