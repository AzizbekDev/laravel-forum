12-Lesson Validation Errors and Old Data
===
### 1) edit `resources/views/layouts/app.blade.php`
---
```
//add this link to navbar
<li>
    <a class="nav-link" href="/threads/create">New threads <span class="sr-only">(current)</span></a>
</li>
```
### 2) edit `resources/views/threads/create.blade.php`
---
```
...
<div class="form-group">
    <label for="channel_id">Choose a channel:</label>
    <select id="channel_id" class="form-control" name="channel_id">
        @foreach (App\Channel::all() as $channel)
            <option value="{{ $channel->id }}">{{ $channel->name }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="title">Title:</label>
    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}">  // add value old('title')
</div>

<div class="form-group">
    <label for="body">Body:</label>
    <textarea name="body" id="body" class="form-control" rows="8">{{ old('body') }}</textarea>  // add value old('body')
</div>
//move button in new from group div
<div class="form-group">
    <button type="submit" class="btn btn-primary">Publish</button>
</div>
// this is a display of form validation errors
@if(count($errors))
    @foreach ($errors->all() as $error)
    <ul class="list-unstyled alert alert-danger">
        <li>{{ $error }}</li>
    </ul>
    @endforeach
@endif
...
```
