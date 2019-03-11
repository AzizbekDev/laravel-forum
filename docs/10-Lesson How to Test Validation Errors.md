10-Lesson How to Test Validation Errors
===
### 1) edit `tests/Feature/CreateThreadsTest.php`
---
```
public function an_authenticated_user_can_create_new_forum_threads()
{
$thread = make('App\Thread'); // use make method insteade of create
$response = $this->post('/threads',$thread->toArray());
$this->get($response->headers->get('Location')) // get thread full path with id
    ->assertSee($thread->body)
    ->assertSee($thread->title);
}
```
### 2) create new testing method in `tests/Feature/CreateThreadsTest.php`
---
```
/** @test */
function a_thread_requires_a_title()
{
    $this->publishThread(['title' => null])
        ->assertSessionHasErrors('title');
}
/** @test */
function a_thread_requires_a_body()
{
    $this->publishThread(['body' => null])
        ->assertSessionHasErrors('body');
}
/** @test */
function a_thread_requires_a_valid_channel()
{
    factory('App\Channel', 2)->create();
    $this->publishThread(['channel_id' => null])
        ->assertSessionHasErrors('channel_id');
    $this->publishThread(['channel_id' => 999])
        ->assertSessionHasErrors('channel_id');
}
protected function publishThread($overrides = [])
{
    $this->withExceptionHandling()->signIn();
    $thread = make('App\Thread', $overrides);
    return $this->post('/threads', $thread->toArray());
}
```
### 3) edit `Controllers/ThreadsController.php`
---
```
public function store(Request $request)
{
     $this->validate($request, [
        'title' => 'required',
        'body'  => 'required',
        'channel_id' => 'required|exists:channels,id'
    ]);
}
```
### 4) edit `Controllers/RepliesController.php`
---
```
public function store($channelId, Thread $thread)
{   
$this->validate(request(), ['body' => 'required']); //add this line
    ...
}
```
### 5) edit `Controllers/PraticipateInForumTest.php`
---
```
function a_reply_requires_a_body()
{
    $this->withExceptionHandling()->signIn();
    $thread = create('App\Thread');
    $reply = make('App\Reply', ['body' => null]);
    $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
}
```