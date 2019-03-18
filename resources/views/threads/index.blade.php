@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            @foreach($threads as $thread)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="{{ $thread->path() }}">
                        {{ $thread->title }}
                    </a>
                    <a href="{{ $thread->path() }}" class="pull-right">
                        {{ $thread->replies_count }} {{ str_plural('reply', $thread->replies_count) }}
                    </a>
                </div>
                <div class="panel-body">
                    <div class="body"><p>{{ $thread->body }}</p></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection