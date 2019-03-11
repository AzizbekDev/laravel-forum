9-Lesson A Thread Should Be Assigned a Channel
===
### 1) edit `tests/Unit/ThreadTest.php`
---
```
/** @test */
public function a_thread_belongs_to_a_channel()
{
    $thread = create('App\Thread');
    $this->assertInstanceOf('App\Channel', $thread->channel);
}

/** @test */
public function a_thread_can_make_a_string_path()
{
    $thread = create('App\Thread');
    $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->id}", $thread->path());
}
```
### 2) create new model named Channel
---
`php artisan make:model Channel -m`

### 3) edit `app/Thread.php`
---
```
// edit this method path
public function path()
{
    return "/threads/{$this->channel->slug}/{$this->id}"; 
}
// add new relation belongsTo
public function channel()
{
    return $this->belongsTo(Channel::class);
}
```
### 4) edit `database/migrations/create_threads_table.php`
---
```
$table->unsignedInteger('user_id');
$table->unsignedInteger('channel_id');
```
### 5) edit `database/factories/UserFactory.php`
---
```
$factory->define(App\Thread::class, function($faker){
    return[
        'user_id' => function(){
            return factory('App\User')->create()->id;
        },
        'channel_id' =>function(){
            return factory('App\Channel')->create()->id;
        },
        'title' => $faker->sentence,
        'body'=> $faker->paragraph
    ];
});

$factory->define(App\Channel::class, function($faker){
    $name = $faker->word;
    return [
        'name' => $name,
        'slug' => $name,
    ];
});
```
### 6) edit `database/migrations/create_channels_table.php`
---
```
Schema::create('channels', function (Blueprint $table) {
    $table->increments('id');
    $table->string('name',50);
    $table->string('slug',50);
    $table->timestamps();
});
```
### 6) edit `controllers/ThreadsController.php`
---
```
public function store(Request $request)
{
    $thread = Thread::create([
        'user_id' => auth()->id(),
        'channel_id' => $request->channel_id, // add this line
        'title' => $request->title,
        'body' => $request->body
    ]);
    return redirect($thread->path());
}

public function show($channelId, Thread $thread)
{
    return view('threads.show', compact('thread'));
}
```
### 7) edit `controllers/RepliesController.php`
---
``` 
// add new param  $channelId
public function store($channelId, Thread $thread)
{  
$thread->addReply([
    'body' => request('body'),
    'user_id' => auth()->id()
]);
return back();
}
```
### 8) run terminal `php artisan migrate:refresh` and `php artisan db:seed`

### 9) edit `routes/web.php`
---
```
Route::resource('threads', 'ThreadsController');
Route::post('/threads/{thread}/replies', 'RepliesController@store');
```
change to
```
Route::get('threads', 'ThreadsController@index');
Route::get('threads/create', 'ThreadsController@create');
Route::get('threads/{channel}/{thread}', 'ThreadsController@show');
Route::post('threads', 'ThreadsController@store');
Route::post('/threads/{channel}/{thread}/replies', 'RepliesController@store');
```

### 10) edit `tests/Feature/CreateThreadsTest.php`
---
`$thread = make('App\Thread');` change to `$thread = create('App\Thread');`

### 11) edit `tests/Feature/PraticipateInForumTest.php`
---
```
function unauthenticated_users_may_not_add_replies()
{
    $this->expectException('Illuminate\Auth\AuthenticationException');
    $this->post('/threads/1/replies', []);
}
public function an_authenticated_user_may_participate_in_forum_threads()
{
    $this->be($user = create('App\User'));
    $thread = create('App\Thread');
    $reply = make('App\Reply');
    $this->post($thread->path().'/replies', $reply->toArray());
    $this->get($thread->path())->assertSee($reply->body);
}
```
change to
```
function unauthenticated_users_may_not_add_replies()
{
    $this->withExceptionHandling()
    ->post('/threads/chanel/1/replies', [])
    ->assertRedirect('/login');
}
public function an_authenticated_user_may_participate_in_forum_threads()
{
    $this->signIn(); // this line updated
    $thread = create('App\Thread');
    $reply = make('App\Reply');
    $this->post($thread->path().'/replies', $reply->toArray());
    $this->get($thread->path())->assertSee($reply->body);
}
```
### 12) edit `tests/Feature/ReadThreadsTest.php`
---
```  
function a_user_can_read_a_single_thread()
{
    $this->get('/threads/' . $this->thread->id)
        ->assertSee($this->thread->title);
}
function a_user_can_read_replies_that_are_associated_with_a_thread()
{
    $reply = create('App\Reply',['thread_id' => $this->thread->id]);
    $this->get('/threads/' . $this->thread->id)
        ->assertSee($reply->body);
}
```
change to 
```
function a_user_can_read_a_single_thread()
{
    $this->get($this->thread->path()) // this line updated
        ->assertSee($this->thread->title);
}
function a_user_can_read_replies_that_are_associated_with_a_thread()
{
    $reply = create('App\Reply',['thread_id' => $this->thread->id]);
    $this->get($this->thread->path()) // this line updated
        ->assertSee($reply->body);
}
```
