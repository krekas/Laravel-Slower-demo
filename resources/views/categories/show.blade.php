@extends('layouts.main')

@section('title', $category->name)

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>{{ $category->name }}</h1>
        <div>
            <a href="{{ route('categories.edit', $category) }}" class="btn">Edit</a>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card">
        <p><strong>Slug:</strong> {{ $category->slug }}</p>
        @if($category->description)
            <p><strong>Description:</strong> {{ $category->description }}</p>
        @endif
    </div>

    <h2>Posts in this Category</h2>
    @foreach($category->posts as $post)
        <div class="card">
            <h3 class="card-title">{{ $post->title }}</h3>
            <p>By {{ $post->user->name }} | Views: {{ $post->view_count }}</p>
            <p>{{ $post->excerpt }}</p>
            <a href="{{ route('posts.show', $post) }}" class="btn btn-secondary">Read More</a>
        </div>
    @endforeach
@endsection
