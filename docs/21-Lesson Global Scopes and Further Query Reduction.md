21-Lesson Global Scopes and Further Query Reduction
===
### 1) edit `app/Http/Controllers/ThreadsController.php`
---
```
protected function getThreads(Channel $channel, ThreadFilters $filters)
{
    $threads = Thread::latest()->filter($filters); // changed this line
    if($channel->exists){
        $threads->where('channel_id', $channel->id);
    }
    return $threads->get();
}
```
### 2) edit `app/Reply.php`
---
```
// favorite methods removed to favoritable trait
<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Reply extends Model
{
     use Favoritable;
    protected $guarded = [];
    protected $with = ['owner', 'favorites'];
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
```
### 3) create and edit `app/Favoritable.php`
---
```
<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
trait Favoritable
{

    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favorited');
    }

    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];
        if (!$this->favorites()->where($attributes)->exists()) {
            return $this->favorites()->create($attributes);
        }
    }

    public function isFavorited()
    {
        return !! $this->favorites->where('user_id', auth()->id())->count();
    }
    
    public function getFavoritesCountAttribute()
    {
        return $this->favorites->count();
    }
}
```
### 4) edit `app/Thread.php`
---
```
protected $with = ['creator', 'channel']; // added channel
...
public function replies()
{
    return $this->hasMany(Reply::class);
```
