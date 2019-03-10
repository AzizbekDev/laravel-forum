> Lesson-1 Following advanced Laravel series "Let's Build a Forum with TDD" on Laracasts
===
### 1) Setup fresh laravel Project `laravel new form`
### 2) Initial Database Setup With Seeding
---
create model Thread with `migrations` and `controller` with resource. 
```
php artisan make:model Thread -mr
```
create model Reply with `migrations` and `controller`
```
php artisan make:model Reply -mc 
```
### 3) edit migrations `create_threads_table.php`
---
```
public function up(){
    Illuminate\Support\Facades\Schema::create(‘threads’, function(Blueprint $table){
        $table→bigIncrements(‘id’);
        $table→integer(‘user_id’);
        $table→ integer(‘title’);
        $table→string(‘body’);
        $table→timestamps(); 
    });
}
```
### 4) edit migrations `create_replies_table.php`
---
```
public function up(){
	Illuminate\Support\Facades\Schema::create(‘replies’, function(Blueprint $table){
        $table->bigIncrements('id');
        $table->integer('thread_id');
        $table->integer('user_id');
        $table->text('body');
        $table->timestamps(); 
    });
}
```
### 5) edit env file `.env`
---
```
DB_DATABASE=form
DB_USERNAME=root
DB_PASSWORD=123
```
### 6) create new `database` called `form` using cli || workbanch || phpmyadmin
---
```
mysql -uroot -p
mysql > create database form;
php artisan migrate 
// results must be successfully created table threads and replies
```
### 7) edit modelFactory `database/factories/ModelFactory.php` run
---
```
php artisan make:factory ModelFactory
```
```
use Illuminate\Support\Str;
use Faker\Generator as Faker;
$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});
$factory->define(App\Thread::class, function($faker){
    return[
        'user_id' => function(){
            return factory('App\User')->create()->id;
        },
        'title' => $faker->sentence,
        'body'=> $faker->paragraph
    ];
});
$factory->define(App\Reply::class, function($faker){
    return [
        'thread_id' => function(){
            return factory('App\Thread')->create()->id;
        },
        'user_id' => function(){
            return factory('App\User')->create()->id;
        },
        'body' => $faker->paragraph
    ];
});
```
### 8) run factories for threads and replies via tinker
---
```
php artisan tinker
>>> $threads = factory('App\Thread',50)->create()
>>> $threads->each(function($thread){ factory('App\Reply',10)->create(['thread_id' => $thread->id]); });
//after this command we insert fake data to threads and replies tables 
```
> Notes: Delete all data form database tables.
`php artisan migrate:refresh`