19-Lesson The Favorite Button
===
### 1) edit `resources/views/threads/reply.blade.php`
---
```
...
<div class="pull-right">
    <form method="POST" action="/replies/{{ $reply->id }}/favorites">
        {{ csrf_field() }}
        <button class="btn btn-default btn-sm" type="submit" {{ $reply->isFavorided() ? 'disabled' : '' }}>
        {{ $reply->favorites()->count() }} {{ str_plural('Favorite', $reply->favorites()->count()) }}</button>
    </form>
</div>
...
```
### 2) edit `app/Reply.php`
---
```
public function isFavorided()
{   
    return $this->favorites()->where('user_id', auth()->id())->exists();
}
```
### 3) edit `app/Http/Controllers/FavoritesController.php`
---
```
public function store(Reply $reply)
{
    $reply->favorite();
    return back();
}
```