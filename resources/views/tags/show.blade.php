@extends('layouts.main')

@section('title', $tag->name)

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Tag: {{ $tag->name }}</h1>
        <div>
            <a href="{{ route('tags.edit', $tag) }}" class="btn">Edit</a>
            <a href="{{ route('tags.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <h2>Posts with this Tag</h2>
    @foreach($tag->posts as $post)
        <div class="card">
            <h3 class="card-title">{{ $post->title }}</h3>
            <p>By {{ $post->user->name }} | Category: {{ $post->category->name }} | Views: {{ $post->view_count }}</p>
            <p>{{ $post->excerpt }}</p>
            <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary">Read More</a>
        </div>
    @endforeach
@endsection
