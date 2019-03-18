22-Lesson A User Has a Profile
===
### 1) create new test
---
`php artisan make:test ProfilesTest` // run this artisan command

### 2) edit `tests/Feature/ProfilesTest.php`
---
```
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfilesTest extends TestCase
{
    use DatabaseMigrations;
    function a_user_has_a_profile()
    {
        $user = create('App\User');
        $this->get('/profile/{$user->name}')
        ->assertSee($user->name);
    }
    function profiles_dispaly_all_threads_created_by_the_associated_user()
    {
        $user = create('App\User');
        $threads = create('App\Thread', ['user_id' => $user->id]);
        $this->get("/profile/{$user->name}")
        ->assertSee($threads->title)
        ->assertSee($threads->body);
    }
}
```
### 3) edit `routes/web.php`
---
```
Route::get('/profile/{user}', 'ProfilesController@show');
```
### 4) create new controller
---
`php artisan make:controller ProfilesController` // run this artisan command
### 5) edit `app/Http/Controllers/ProfilesController`
---
```
public function show(User $user)
{
   return view('profiles.show', [
        'profileUser' => $user,
        'threads' => $user->threads()->paginate(1)
    ]);
}
```
### 6) create and edit `resources/views/profiles/show.blade.php`
---
```
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="page-header">
            <h1>
                {{ $profileUser->name }}
                <small>Since {{ $profileUser->created_at->diffForHumans() }}</small>
            </h1>
        </div>
        @foreach ($threads as $thread)
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ $thread->title }}
            </div>
            <div class="panel-body">
                {{ $thread->body }}
            </div>
        </div>
        @endforeach
        {{ $threads->links() }}
    </div>
@endsection
```
### 7) edit `app/User.php`
---
```
public function getRouteKeyName()
{
    return 'name';
}

public function threads()
{
    return $this->hasMany(Thread::class)->latest();
}
```
