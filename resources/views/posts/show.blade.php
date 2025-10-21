@extends('layouts.main')

@section('title', $post->title)

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>{{ $post->title }}</h1>
        <div>
            <a href="{{ route('posts.edit', $post) }}" class="btn">Edit</a>
            <a href="{{ route('posts.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card">
        <p>
            <strong>Author:</strong> {{ $post->user->name }} |
            <strong>Category:</strong> {{ $post->category->name }} |
            <strong>Views:</strong> {{ $post->view_count }} |
            <strong>Published:</strong> {{ $post->published_at ? $post->published_at->format('Y-m-d H:i') : 'Not published' }}
        </p>
        <p>
            <strong>Tags:</strong>
            @foreach($post->tags as $tag)
                <span class="badge badge-success">{{ $tag->name }}</span>
            @endforeach
        </p>
        @if($post->excerpt)
            <p><em>{{ $post->excerpt }}</em></p>
        @endif
        <div style="margin-top: 20px;">
            {{ $post->content }}
        </div>
    </div>

    <h2>Comments ({{ $post->comments()->count() }})</h2>

    @auth
        <div class="card">
            <h3>Add a Comment</h3>
            <form action="{{ route('posts.comments.store', $post) }}" method="POST">
                @csrf
                <div class="form-group">
                    <textarea name="content" placeholder="Write your comment..." required></textarea>
                </div>
                <button type="submit" class="btn">Submit Comment</button>
            </form>
        </div>
    @endauth

    @foreach($post->comments as $comment)
        @if(!$comment->parent_id)
            <div class="card">
                <strong>{{ $comment->user->name }}</strong>
                <small>{{ $comment->created_at->diffForHumans() }}</small>
                @if(!$comment->is_approved)
                    <span class="badge badge-warning">Pending approval</span>
                @endif
                <div class="comment-content">
                    {{ $comment->content }}
                </div>

                @foreach($comment->replies as $reply)
                    <div class="comment">
                        <strong>{{ $reply->user->name }}</strong>
                        <small>{{ $reply->created_at->diffForHumans() }}</small>
                        @if(!$reply->is_approved)
                            <span class="badge badge-warning">Pending approval</span>
                        @endif
                        <div class="comment-content">
                            {{ $reply->content }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
@endsection
