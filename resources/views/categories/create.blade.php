@extends('layouts.main')

@section('title', 'Create Category')

@section('content')
    <h1>Create Category</h1>

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description">{{ old('description') }}</textarea>
            @error('description')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn">Create</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
