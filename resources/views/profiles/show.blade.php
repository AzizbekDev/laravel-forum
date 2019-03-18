@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="col-md-8 col-md-offset-2">
            <div class="page-header">
                <h1>
                    {{ $profileUser->name }}
                    <small>Since {{ $profileUser->created_at->diffForHumans() }}</small>
                </h1>
            </div>
            @foreach ($threads as $thread)
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a href="#">{{ $thread->creator->name }}</a> posted: {{ $thread->title }}
                    <div class="pull-right">
                        {{ $thread->created_at->diffForHumans() }}
                    </div>
                </div>
                <div class="panel-body">
                    {{ $thread->body }}
                </div>
            </div>
            @endforeach
            {{ $threads->links() }}
        </div>
    </div>
@endsection