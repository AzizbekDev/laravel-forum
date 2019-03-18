24-Lesson Authorization With Policies
===
### 1) edit `resources/views/threads/index.blade.php`
---
```
@foreach($threads as $thread)
...
@endforeach
```
change to
```
@forelse($threads as $thread)
...
@empty
    <p>There are no relevant results at this time.</p>
@endforelse
```
### edit `tests/Feature/CreateThreadsTest.php`
---
```
function unauthorized_users_may_not_delete_threads(){   // guests_cannot_delete_threads old method name changed
    $this->withExceptionHandling();
    $thread = create('App\Thread');
    $this->delete($thread->path())->assertRedirect('/login');
    // if authenticated user try to delete not own threads
    $this->signIn(); 
    $this->delete($thread->path())->assertStatus(403);
}

function authorized_users_can_delete_threads(){  // a_thread_can_be_deleted old method name changed
    $this->signIn();
    $thread = create('App\Thread',['user_id' => auth()->id()]); // add authenticated user_id
    $reply = create('App\Reply', ['thread_id'=> $thread->id]);
    $response = $this->json('DELETE', $thread->path());
    $response->assertStatus(204);
    $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
    $this->assertDatabaseMissing('replies',['id' => $reply->id]);
}
```
### 3) edit `app/Http/Controllers/ThreadsController.php`
---
```
public function destroy($channel, Thread $thread)
{
if($thread->user_id != auth()->id()){
    abort(403, 'You do not have permission to do this..');
}
...
}
```
### 4) create new policy
---
`php artisan make:policy ThreadPolicy --model=Thread` // run this artisan command
### 5) edit `app/Policies/ThreadPolicy.php`
---
```
public function update(User $user, Thread $thread)
{
    return $thread->user_id == $user->id;
}
```
### 6) edit `app/Providers/AuthServiceProvider.php`
---
```
protected $policies = [
    'App\Thread' => 'App\Policies\ThreadPolicy',
];
```
### 7) edit `app/Http/Controllers/ThreadsController.php`
---
```
if($thread->user_id != auth()->id()){
    abort(403, 'You do not have permission to do this..');
}
```
change to 
```
$this->authorize('update', $thread);
```
### 8) edit `resources/views/threads/show.blade.php`
---
```
...
@can('update', $threads) // add can operator
<div class="pull-right">
    <form method="POST" action="{{ $thread->path() }}">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <button class="btn btn-danger btn-sm" type="submit">Delete</button>
    </form>
</div>
@endcan
...
```
### 9) edit `app/Providers/AuthServiceProvider.php`
---
```
public function boot()
{
    ...
    
    // this method used for the user added name is may access all authorize method. like administrator
    Gate::before(function($user){
        // if( $user->name === 'azizbek') return true;
    });
}
```