20-Lesson From 56 Queries Down to 2
===
### 1) install debugbar using composer
---
`composer require barryvdh/laravel-debugbar`

### 2) edit `app/Providers/AppServiceProvider.php`
---
```
public function boot()
{
    \View::composer('*', function($view){
        $channels = \Cache::rememberForever('channels', function(){
            return Channel::all();
        });
        $view->with('channels', $channels);
    });
}
public function register()
{
    if($this->app->isLocal()){
        $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
    }
}
```
### 3) edit `app/Http/Controllers/ThreadsController.php`
---
```
protected function getThreads(Channel $channel, ThreadFilters $filters)
{
    $threads = Thread::with('channel')->latest()->filter($filters); // add with('channel')
    ...
}
```
### 4) edit `app/Thread.php`
---
```
public function replies()
{
    return $this->hasMany(Reply::class)
    ->withCount('favorites')
    ->with('owner');
}
```
### 5) edit `resources/views/threads/reply.blade.php`
---
```
{{ $reply->favorites()->count() }} {{ str_plural('Favorite', $reply->favorites()->count()) }}
```
change to
```
{{ $reply->favorites_count }} {{ str_plural('Favorite', $reply->favorites_count) }}
```