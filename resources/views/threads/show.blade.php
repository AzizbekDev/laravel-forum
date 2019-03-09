@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Single thread</div>
                <div class="card-body">
                    <article>
                        <h4>{{ $thread->title }}</h4>
                        <div class="body"><p>{{ $thread->body }}</p></div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection