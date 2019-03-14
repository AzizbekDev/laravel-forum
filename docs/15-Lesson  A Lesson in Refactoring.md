15-Lesson  A Lesson in Refactoring
===
### 1) edit `app/Http/Controllers/ThreadsController.php`
---
```
use App\Filters\ThreadFilters;
...
public function index(Channel $channel, ThreadFilters $filters)
{
    $threads = $this->getThreads($channel, $filters);
    return view('threads.index',compact('threads'));
}
...
protected function getThreads(Channel $channel, ThreadFilters $filters)
{
    $threads = Thread::latest()->filter($filters);
    if($channel->exists){
        $threads->where('channel_id', $channel->id);
    }
    return $threads->get();
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
    protected $request, $builder, $filters = [];;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        $this->builder = $builder;
        foreach($this->getFilters() as $filter => $value){
            if(method_exists($this, $filter)){
                $this->$filter($value);
            }
        }
        return $this->builder;
    }

    public function getFilters(){
        $filters = array_intersect(array_keys($this->request->all()), $this->filters);
        return $this->request->only($filters);
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
    protected $filters = ['by'];

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
use App\Filters\ThreadFilters;
use Illuminate\Database\Eloquent\Builder;
...
public function scopeFilter($query, ThreadFilters $filters)
{
    return $filters->apply($query);
}
```
