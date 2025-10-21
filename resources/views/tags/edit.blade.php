@extends('layouts.main')

@section('title', 'Edit Tag')

@section('content')
    <h1>Edit Tag</h1>

    <form action="{{ route('tags.update', $tag) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $tag->name) }}" required>
            @error('name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn">Update</button>
        <a href="{{ route('tags.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
