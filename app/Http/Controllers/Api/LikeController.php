<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggleLike(Post $post, Request $request)
    {
        $userId = auth()->id();

        $existingLike = $post->likes()
            ->where('user_id', $userId)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            return response()->json([
                'status' => true,
                'message' => 'Unliked',
                'likes_count' => $post->likes()->count(),
            ]);
        }

        $post->likes()->create([
            'user_id' => $userId,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Liked',
            'likes_count' => $post->likes()->count(),
        ]);
    }
}
