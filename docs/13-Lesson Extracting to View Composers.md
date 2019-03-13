13-Lesson Extracting to View Composers
===
### 1) edit `app/Providers/AppServiceProvider.php`
---
```
use App\Channel;
...
public function boot()
{
    \View::share('channels', Channel::all());
}
```
### 2) edit `resources/views/layouts/app.blade.php` and `resources/views/threads/create.blade.php`
---
```
@foreach (App\Channel::all() as $channel)
```
change to
```
@foreach ($channels as $channel)
...
