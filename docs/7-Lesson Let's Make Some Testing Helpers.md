> Lesson-7 Let's Make Some Testing Helpers
===
### 1)edit composer.json
in the autoload-dev object after psr-4 add below lines
---
```
"files": ["tests/Utilities/functions.php"]
```
### 2)create functions.php file in the utilities folder add bellow some functions \
---
```
<?php
function create($class, $attribute = [])
{
    return factory($class)->create($attribute);
}
function make($class, $attribute = [])
{
    return factory($class)->make($attribute);
}
```
### 3) run cli
---
```#~ composer dump-autoload```

### 4)edit in all tests old method to new one
---
```
$thread = factory('App\Thread')->make(); change to $thread = make('App\Thread');
$reply = factory('App\Reply')->create(); change to $reply = create('App\Reply');
```
### 5)edit CreateThreadsTest.php
---
```
$this->actingAs(create('App\User')); change to $this->signIn();
```
### 6) edit tests/TestCase.php
---
```
protected function signIn($user = null)
{
    $user = $user ?: create('App\User');
    $this->actingAs($user);
    return $this;
}
```
