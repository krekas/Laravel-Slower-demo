<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        // BAD: No eager loading - will cause N+1 for user, category, tags, and comments
        // BAD: Filtering without proper indexes
        // BAD: No pagination
        $posts = Post::where('is_published', true)
            ->orderBy('published_at', 'desc')
            ->get();

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        // BAD: Loading all categories and tags without considering scale
        $categories = Category::all();
        $tags = Tag::all();

        return view('posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();
        $validated['published_at'] = $validated['is_published'] ?? false ? now() : null;

        $post = Post::create($validated);

        if (isset($validated['tags'])) {
            $post->tags()->attach($validated['tags']);
        }

        return redirect()->route('posts.index')->with('success', 'Post created successfully!');
    }

    public function show(Post $post)
    {
        // BAD: No eager loading - will cause N+1 queries for:
        // - post's user
        // - post's category
        // - post's tags
        // - post's comments
        // - each comment's user
        // - each comment's replies

        // BAD: Update query without considering concurrent requests
        $post->increment('view_count');

        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        // BAD: Loading all categories and tags
        $categories = Category::all();
        $tags = Tag::all();

        return view('posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        if (($validated['is_published'] ?? false) && !$post->published_at) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return redirect()->route('posts.index')->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }
}
