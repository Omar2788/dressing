<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function getComments($mealId)
    {
        $comments = Comments::where('meal_id', $mealId)->with('user')->get();

        return response()->json($comments, 200);
    }

    public function addComment(Request $request)
    {
        $user = Auth::user();
        $mealId = $request->input('meal_id');
        $text = $request->input('text');

        $comment = new Comments([
            'user_id' => $user->id,
            'meal_id' => $mealId,
            'text' => $text,
        ]);

        $comment->save();

        return response()->json(['message' => 'Comment added successfully'], 200);
    }
}
