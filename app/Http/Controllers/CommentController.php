<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a new comment on a blog.
     */
    public function store(Request $request, Blog $blog)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        Comment::create([
            'blog_id' => $blog->id,
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'parent_id' => null,
        ]);

        return redirect()
            ->route('blogs.show', $blog)
            ->with('success', 'Comment posted successfully.');
    }

    /**
     * Store a reply to an existing comment.
     */
    public function reply(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        Comment::create([
            'blog_id' => $comment->blog_id,
            'user_id' => auth()->id(),
            'content' => $validated['content'],
            'parent_id' => $comment->id,
        ]);

        return redirect()
            ->route('blogs.show', $comment->blog_id)
            ->with('success', 'Reply posted successfully.');
    }
}