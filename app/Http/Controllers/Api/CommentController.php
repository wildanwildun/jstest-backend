<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'comments' => 'required|string'
        ]);

        $comment = Comment::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
            'comments' => $request->comments,
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load('user'),
        ], 201);
    }

    public function like($id)
    {
        $comment = Comment::findOrFail($id);
        $userId = auth()->id();

        $existing = $comment->likes()->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['message' => 'Unliked']);
        } else {
            $comment->likes()->create(['user_id' => $userId]);
            return response()->json(['message' => 'Liked']);
        }
    }

}

