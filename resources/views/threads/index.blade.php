@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">From Threads</div>
                <div class="card-body">
                    @foreach($threads as $thread)
                        <article>
                            <a href="{{ $thread->path() }}"><h4>{{ $thread->title }}</h4></a>
                            <div class="body"><p>{{ $thread->body }}</p></div>
                        </article><hr>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection