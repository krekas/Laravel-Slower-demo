@extends('layouts.main')

@section('title', 'Comments')

@section('content')
    <h1>All Comments</h1>

    @foreach($comments as $comment)
        <div class="card">
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <strong>{{ $comment->user->name }}</strong> on
                    <a href="{{ route('posts.show', $comment->post) }}">{{ $comment->post->title }}</a>
                    <br>
                    <small>{{ $comment->created_at->diffForHumans() }}</small>
                </div>
                <div>
                    @if($comment->is_approved)
                        <span class="badge badge-success">Approved</span>
                    @else
                        <span class="badge badge-warning">Pending</span>
                        <form action="{{ route('comments.approve', $comment) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn">Approve</button>
                        </form>
                    @endif
                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </div>
            </div>
            <div class="comment-content">
                {{ $comment->content }}
            </div>
        </div>
    @endforeach
@endsection
