@extends('layouts.main')

@section('title', 'Create Tag')

@section('content')
    <h1>Create Tag</h1>

    <form action="{{ route('tags.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn">Create</button>
        <a href="{{ route('tags.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
@endsection
