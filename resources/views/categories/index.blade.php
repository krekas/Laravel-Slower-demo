@extends('layouts.main')

@section('title', 'Categories')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>Categories</h1>
        <a href="{{ route('categories.create') }}" class="btn">Create Category</a>
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
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td>{{ $category->posts()->count() }}</td>
                    <td>
                        <a href="{{ route('categories.show', $category) }}" class="btn btn-secondary">View</a>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-secondary">Edit</a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" style="display: inline;">
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
