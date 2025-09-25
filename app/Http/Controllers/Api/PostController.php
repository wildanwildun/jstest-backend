<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $post = Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $post
        ]);
    }

    public function index(Request $request)
    {
        $search = $request->query('search');
        $idPost = $request->query('idPost') ?? '';
        $posts = Post::with(['user:id,name,email', 'comments'])
            ->withCount('likes')
            ->when($search, fn($q) => $q->where(function ($s) use ($search) {
                $s->where('title', 'like', "%$search%")
                ->orWhere('content', 'like', "%$search%");
            }))
            ->when($idPost, function ($query) use ($idPost) {
                $query->where('id', $idPost);
            })
            ->latest()
            ->paginate(10);

        return response()->json($posts);
    }
}
