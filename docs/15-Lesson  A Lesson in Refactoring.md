15-Lesson  A Lesson in Refactoring
===
### 1) edit `app/Http/Controllers/ThreadsController.php`
---
```
use App\Filters\ThreadFilters;
...
public function index(Channel $channel, ThreadFilters $filters)
{
    $threads = Thread::filter($filters)->get();
    
    return view('threads.index',compact('threads'));
}
...
protected function getThreads(Channel $channel) // App\Queries\ThreadQuery
{
    if($channel->exists){
        $threads = $channel->threads()->latest();
    }else{
        $threads = Thread::latest();
    }
    $threads = $threads->get();
    return $threads;
}

```
### 2) create and edit `app/Filters/Filters.php`
---
```
<?php 
namespace App\Filters;

use Illuminate\Http\Request;

abstract class Filters
{
    protected $request, $builder;

    /**
     * Filters constructor
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        $this->builder = $builder;
        if($this->request->has('by')){
            $this->by($this->request->by);
        }
        return $this->builder;
    }
}
```
### 3) create and edit `app/Filters/ThreadFilters.php`
---
```
<?php 
namespace App\Filters;

use App\User;

class ThreadFilters extends Filters
{
    /**
     * Filter The Query by a given a username
     * @param string $username
     * @return mixed
     */
    protected function by($username)
    {
        $user = User::where('name', $username)->firstOrFail();
        return $this->builder->where('user_id', $user->id);
    }
}
```
### 4) edit and edit `app/Thread.php`
---
```
public function scopeFilter($query, $filters)
{
    return $filters->apply($query);
}
```
