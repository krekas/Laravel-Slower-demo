<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Slower Demo')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; background: #f4f4f4; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        nav { background: #333; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        nav a { color: white; text-decoration: none; margin-right: 20px; }
        nav a:hover { text-decoration: underline; }
        h1, h2, h3 { margin-bottom: 15px; color: #333; }
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .btn { display: inline-block; padding: 8px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .form-group textarea { min-height: 100px; }
        .card { border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; }
        .card-title { font-size: 1.2em; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table th, table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background: #f8f9fa; font-weight: bold; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 0.8em; }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .comment { margin-left: 20px; padding-left: 15px; border-left: 3px solid #ddd; margin-bottom: 10px; }
        .comment-content { background: #f8f9fa; padding: 10px; border-radius: 4px; margin-top: 5px; }
    </style>
</head>
<body>
    <nav>
        <a href="{{ route('posts.index') }}">Posts</a>
        <a href="{{ route('categories.index') }}">Categories</a>
        <a href="{{ route('tags.index') }}">Tags</a>
        <a href="{{ route('comments.index') }}">Comments</a>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
