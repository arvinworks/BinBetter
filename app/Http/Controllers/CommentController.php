<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PostComment;
use App\Models\PostReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = PostComment::with(['resident', 'replies.resident'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['comments' => $comments]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user = User::getCurrentUser();

        $validated = $request->validate([
            'comment' => 'required|string|max:500',
            'post_report_id' => 'required|integer|exists:post_reports,id', // Post must exist
            'parent_id' => 'nullable|integer|exists:post_comments,id' // Parent comment must exist if it's a reply
        ]);

        // Create the comment or reply
        $comment = PostComment::create([
            'resident_id' => $user->id,
            'post_report_id' => $validated['post_report_id'], // Post ID where comment is posted
            'parent_id' => $validated['parent_id'] ?? null, // Null if it's not a reply
            'comment' => $validated['comment']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment posted successfully',
            'comment' => $comment
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Check if the ID is valid
        dd($id); // Dump the ID to see what you're getting

        // Find the comment by ID
        $comment = PostComment::find($id);

        // Check if the comment exists
        if (!$comment) {
            // Log the error to help debugging
            Log::info("Comment with ID {$id} not found.");
            return response()->json(['error' => 'Comment not found'], 404);
        }

        // Delete the comment
        $comment->delete();

        return response()->json(['success' => true, 'message' => 'Comment deleted successfully']);
    }


    public function handleLikeDislike($action, $commentId)
    {
        $comment = PostComment::find($commentId);

        if (!$comment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }

        // Assuming you have `likes` and `dislikes` columns in your comments table
        if ($action === 'like') {
            $comment->increment('likes');
        } elseif ($action === 'dislike') {
            $comment->increment('dislikes');
        } else {
            return response()->json(['error' => 'Invalid action'], 400);
        }

        $comment->save();

        return response()->json(['message' => 'Action successful'], 200);
    }
}
