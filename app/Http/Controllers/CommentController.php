<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        // BAD: No eager loading - N+1 for post and user
        // BAD: No pagination
        // BAD: Filtering on unindexed column
        $comments = Comment::where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('comments.index', compact('comments'));
    }

    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $validated['post_id'] = $post->id;
        $validated['user_id'] = auth()->id();
        $validated['is_approved'] = false; // Needs approval

        Comment::create($validated);

        return redirect()->route('posts.show', $post)->with('success', 'Comment added successfully!');
    }

    public function approve(Comment $comment)
    {
        $comment->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Comment approved!');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully!');
    }
}
