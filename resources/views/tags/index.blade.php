@extends('layouts.main')

@section('title', 'Tags')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Tags</h1>
        <a href="{{ route('tags.create') }}" class="btn">Create Tag</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Posts Count</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tags as $tag)
                <tr>
                    <td>{{ $tag->name }}</td>
                    <td>{{ $tag->slug }}</td>
                    <td>{{ $tag->posts()->count() }}</td>
                    <td>
                        <a href="{{ route('tags.show', $tag) }}" class="btn btn-secondary">View</a>
                        <a href="{{ route('tags.edit', $tag) }}" class="btn btn-secondary">Edit</a>
                        <form action="{{ route('tags.destroy', $tag) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
