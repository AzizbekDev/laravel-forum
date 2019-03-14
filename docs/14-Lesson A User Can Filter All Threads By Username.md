14-Lesson A User Can Filter All Threads By Username
===
### 1) edit `tests/Feature/ReadThreadsTest.php`
---
```
function a_user_can_filter_by_a_user_name()
{
$this->signIn(create('App\User',['name' => 'azizbek']));
    $threadByAzizbek = create('App\Thread', ['user_id' => auth()->id()]);
    $threadNotByAzizbek = create('App\Thread');
    $this->get('threads?by=azizbek')
        ->assertSee($threadByAzizbek->title)
        ->assertDontSee($threadNotByAzizbek->title);
}
```
### 2) edit `app/Providers/AppServiceProvider.php`
---
```
public function boot()
{
    \View::composer('*', function($view){
        $view->with('channels', Channel::all());
    });
}
```
### 3) edit `app/Http/Controllers/ThreadsController.php`
---
```
public function index(Channel $channel)
{
    if($channel->exists){
        $threads = $channel->threads()->latest();
    }else{
        $threads = Thread::latest();
    }
    if($username = request('by'))
    {
        $user = User::where('name', $username)->firstOrFail();
        $threads->where('user_id', $user->id);
    }
    $threads = $threads->get();
    
    return view('threads.index',compact('threads'));
}
```
### 4) edit `resources/views/layouts/app.blade.php`
---
```
<li>
    <a class="nav-link" href="/threads">All threads <span class="sr-only">(current)</span></a>
</li>
```
change to
```
<li class="dropdown">
    <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Browse <span class="caret"></span></a>
    <ul class="dropdown-menu">
        <li><a class="nav-link" href="/threads">All threads <span class="sr-only">(current)</span></a></li>
        @if(auth()->check())
            <li><a class="nav-link" href="/threads?by={{ auth()->user()->name }}">My threads <span class="sr-only">(current)</span></a></li>
        @endif
    </ul>
</li>
```
