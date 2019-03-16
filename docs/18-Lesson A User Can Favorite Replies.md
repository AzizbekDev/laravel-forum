18-Lesson A User Can Favorite Replies
===
### 1) create and edit `tests/Feature/FavoritesTest.php`
---
```
<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FavoritesTest extends TestCase
{
use DatabaseMigrations;
   /** @test */
    public function an_authenticated_user_can_favorite_any_reply()
    {
        $this->signIn();
        $reply = create('App\Reply');
        $this->post('replies/'.$reply->id.'/favorites');
        $this->assertCount('1', $reply->favorites);
    }
    /** @test */
    public function guests_can_not_favorite_anything()
    {
        $this->withExceptionHandling()
            ->post('replies/1/favorites')
            ->assertRedirect('/login');
    }
    /** @test */
    public function an_authenticated_user_may_only_favorite_a_reply_once()
    {
        $this->signIn();
        $reply = create('App\Reply');
        try {
            $this->post('replies/'.$reply->id.'/favorites');
            $this->post('replies/'.$reply->id.'/favorites');
        } catch (\Exception $e) {
            $this->fail('Did not expect to insert the same record set twice.');
        }
        $this->assertCount(1, $reply->favorites);
    }
}
```
### 2) edit `routes/web.php`
---
```
Route::post('/replies/{reply}/favorites', 'FavoritesController@store');
```
### 3) create new controller `FavoritesController`
---
`php artisan make:controller FavoritesController` // run this artisan command

### 4) edit `app/Http/Controllers/FavoritesController.php`
---
```
<?php

namespace App\Http\Controllers;

use App\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoritesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Reply $reply)
    {
     return $reply->favorite();
    }
}
```
### 5) create new migration
---
`php artisan make:migration create_favorites_table --create=favorites` //run this artisan command

### 6) edit `database/migrations/create_favorites_table.php`
---
```
...
Schema::create('favorites', function (Blueprint $table) {
    $table->increments('id');
    $table->unsignedInteger('user_id');
    $table->unsignedInteger('favorited_id');
    $table->string('favorited_type', 50);
    $table->timestamps();
    $table->unique(['user_id', 'favorited_id', 'favorited_type']);
});
...
```
### 7) edit `app/Reply.php`
---
```
// add new relationship morphMany
public function favorites()
{
    return $this->morphMany(Favorite::class, 'favorited');
}
// creating user favorite reply
public function favorite()
{
    $attribute = ['user_id' => auth()->id()];
    if(! $this->favorites()->where($attribute)->exists()){
        $this->favorites()->create(['user_id' => auth()->id()]);
    }
}
```
### 8) create new model named Favorite
---
`php artisan make:model Favorite` //run this artisan command
### 9) edit `app\Favorite.php`
---
```
class Favorite extends Model
{
    protected $guarded = []; // add this variable array empty
}
```