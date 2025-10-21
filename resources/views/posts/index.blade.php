@extends('layouts.main')

@section('title', 'Posts')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Posts</h1>
        <a href="{{ route('posts.create') }}" class="btn">Create Post</a>
    </div>

    @foreach($posts as $post)
        <div class="card">
            <h3 class="card-title">{{ $post->title }}</h3>
            <p>
                By {{ $post->user->name }} |
                Category: {{ $post->category->name }} |
                Views: {{ $post->view_count }} |
                Comments: {{ $post->comments()->count() }}
            </p>
            <p>
                Tags:
                @foreach($post->tags as $tag)
                    <span class="badge badge-success">{{ $tag->name }}</span>
                @endforeach
            </p>
            <p>{{ $post->excerpt }}</p>
            <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary">Read More</a>
            <a href="{{ route('posts.edit', $post) }}" class="btn btn-secondary">Edit</a>
            <form action="{{ route('posts.destroy', $post) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
        </div>
    @endforeach
@endsection
