<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display all blogs.
     */
    public function index()
    {
        $blogs = Blog::with('user')
            ->latest()
            ->get();

        return view('blogs.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new blog.
     */
    public function create()
    {
        return view('blogs.create');
    }

    /**
     * Store a newly created blog.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('blogs', 'public');
        }

        Blog::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image' => $imagePath,
        ]);

        return redirect()
            ->route('blogs.index')
            ->with('success', 'Blog post created successfully.');
    }

    /**
     * Display a specific blog.
     */
    public function show(Blog $blog)
    {
        $blog->load([
            'user',
            'comments' => function ($query) {
                $query->whereNull('parent_id')
                    ->with([
                        'user',
                        'replies.user'
                    ])
                    ->latest();
            }
        ]);

        return view('blogs.show', compact('blog'));
    }
    public function edit(Blog $blog)
    {
        //
    }

    public function update(Request $request, Blog $blog)
    {
        //
    }

    public function destroy(Blog $blog)
    {
        //
    }
}