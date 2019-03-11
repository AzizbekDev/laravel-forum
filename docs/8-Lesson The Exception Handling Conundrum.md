8-Lesson The Exception Handling Conundrum
===
### 1)edit `routes/web.php`
---
```
Route::get('/threads', 'ThreadsController@index');
Route::get('/threads/create', 'ThreadsController@create');
Route::get('/threads/{thread}', 'ThreadsController@show');
Route::post('/threads', 'ThreadsController@store');
```
change to
```
Route::resource('threads', 'ThreadsController');
```

### 2) create and edit `resources/views/threads/create.blade.php`
---
```
@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Create a New Thread</div>
                <div class="panel-body">
                <form method="POST" action="/threads">
                    <div class="form-group">
                        <label for="title" class="form-control">Title</label>
                        <input id="title" class="form-control" name="title" type="text">
                    </div>
                    <div class="form-group">
                        <label for="body" class="form-control">Body</label>
                        <textarea id="body" class="form-control" name="body" rows="5"></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Create</button>
                </form></div>
            </div>
        </div>
    </div>
</div>
@endsection
```
### 3) edit `ThreadsController.php`
---
```
$this->middleware('auth')->only('store');
```
change to
```
$this->middleware('auth')->except(['index','show']);
```
### 4) edit `CreateThreadsTest.php`
---
```
/** @test */
public function guests_may_not_create_threads()
{
    $this->withExceptionHandling();
    $this->get('/threads/create')
        ->assertRedirect('/login');
    $this->post('/threads', [])
        ->assertRedirect('/login');
}
    
```
### 5) edit `Exceptions\Handler.php`
---
```
    // if (app()->environment() === 'testing') throw $exception; comment this line
```
### 6) edit `tests\TestCase.php`
---
```
use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
...
protected function setUp()
{
    parent::setUp();
    $this->disableExceptionHandling();
}
protected function disableExceptionHandling()
{
    $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);
    $this->app->instance(ExceptionHandler::class, new class extends Handler {
        public function __construct() {}
        public function report(\Exception $e) {}
        public function render($request, \Exception $e) {
            throw $e;
        }
    });
}    
protected function withExceptionHandling()
{
    $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);
    return $this;
}
```