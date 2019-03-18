<div class="panel panel-default">
    <div class="panel-heading">
        <a href="{{ route('profile', $reply->owner) }}">
            {{ $reply->owner->name }}
        </a> said {{ $reply->created_at->diffForHumans() }}...
        <div class="pull-right">
            <form method="POST" action="/replies/{{ $reply->id }}/favorites">
                {{ csrf_field() }}
                <button class="btn btn-default btn-sm" type="submit" {{ $reply->isFavorited() ? 'disabled' : '' }}>
                {{ $reply->favorites_count }} {{ str_plural('Favorite', $reply->favorites_count) }}</button>
            </form>
        </div>
    </div>

    <div class="panel-body">
        {{ $reply->body }}
    </div>
</div>