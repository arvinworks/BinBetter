<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PostGarbageTipComment;
use App\Models\ReportGarbageTip;
use App\Models\GarbageTip;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GarbageTipsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $page = "Garbage Tips";
        return view('pages.back.v_garbagetips', compact('page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $garbagetips = GarbageTip::whereNull('deleted_at')->get();

        $formattedData = $garbagetips->map(function ($item) {
            // Split the photos by comma and trim whitespace
            $photos = array_map('trim', explode(',', $item->photos));

            // Limit to the first two photos
            $photosToShow = array_slice($photos, 0, 4);

            // Prepare photo HTML
            $photosHtml = '';
            foreach ($photosToShow as $photo) {
                // Ensure the photo URL is valid before rendering
                if (!empty($photo)) {
                    $photosHtml .= '<img src="' . e($photo) . '" alt="Garbage Tip Photo" class="avatar avatar-sm mx-1" style="width: 100px; height: 100px;">';
                }
            }

            // Ensure at least two images are displayed, if not add a placeholder
            while (count($photosToShow) < 4) {
                $photosHtml .= '<img src="assets/back/images/brand/logo/noimage.jpg" alt="No Image" class="avatar avatar-sm mx-1" style="width: 100px; height: 100px;">';
                $photosToShow[] = 'assets/back/images/brand/logo/noimage.jpg';
            }

            return [
                'title' => e($item->title),
                'photos' => $photosHtml,
                'video' => $item->video,
                'description' => $item->description,
                'actions' => '
                    <a class="edit-btn" href="javascript:void(0)" 
                        data-id="' . e($item->id) . '"
                        data-title="' .  $item->title . '"
                        data-photos="' .  $item->photos . '"
                        data-video="' . $item->video . '"
                        data-description="' . $item->description . '"
                        data-modaltitle="Edit"><i class="bi bi-pencil-square fs-3"></i></a>
                    <a class="delete-btn" href="javascript:void(0)" data-id="' . e($item->id) . '"><i class="bi bi-trash fs-3"></i></a>'
            ];
        });

        return response()->json(['data' => $formattedData]);
    }

    public function comment_api()
    {
        $posts_garbagetip = GarbageTip::with([
            'user',
            'garbage_reports',
            'post_garbagetip_comments.user',
            'post_garbagetip_comments.replies_garbagetip',
            'post_garbagetip_comments.replies_garbagetip.user'
        ])
            ->whereHas('garbage_reports', function ($query) {
                $query->where('report_status', 0);
            })
            ->orWhereDoesntHave('garbage_reports')
            ->get();

        if ($posts_garbagetip) {
            return response()->json(['posts_garbagetip' =>  $posts_garbagetip]);
        } else {
            return response()->json(['error' => 'Post garbage tip not found'], 404);
        }
    }

    public function comment_garbagetip(Request $request)
    {
        $user = User::getCurrentUser();

        $validated = $request->validate([
            'comment' => 'required|string|max:500',
            'garbagetip_id' => 'required|integer|exists:garbage_tips,id',
            'parent_id' => 'nullable|integer|exists:post_garbagetip_comments,id'
        ]);

        // Create the comment or reply
        $comment = PostGarbageTipComment::create([
            'user_id' => $user->id,
            'garbage_tip_id' => $validated['garbagetip_id'],
            'parent_id' => $validated['parent_id'] ?? null,
            'comment' => $validated['comment']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment posted successfully',
            'comment' => $comment
        ]);
    }

    public function garbagetip_handleLikeDislike($action, $commentId)
    {
        $comment = PostGarbageTipComment::find($commentId);

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'title' => 'required|string|max:255',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'video' => 'nullable|url',
            'description' => 'required|string'
        ]);

        // Store the uploaded photos if any
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $imgFile) {
                // Create a unique filename for each photo
                $filename = Str::random(10) . '_' . time() . '.' . $imgFile->getClientOriginalExtension();
                $imagePath = 'assets/uploads/' . $filename;

                $imgFile->move(public_path('assets/uploads'), $filename);

                $photoPaths[] = $imagePath;
            }
        }

        $garbageTip = GarbageTip::create([
            'title' => $request->title,
            'photos' => implode(',', $photoPaths),
            'video' => $request->video,
            'description' => $request->description
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Garbage tip added successfully!',
            'data' => $garbageTip
        ]);
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
        $request->validate([
            'title' => 'required|string|max:255',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'video' => 'nullable|url',
            'description' => 'required|string'
        ]);

        $garbageTip = GarbageTip::findOrFail($id);

        // Initialize an array for storing photo paths
        $photoPaths = $garbageTip->photos ? explode(',', $garbageTip->photos) : [];

        if ($request->hasFile('photos')) {
            // Check and delete existing photos only if there are any
            if (!empty($garbageTip->photos)) {
                $existingPhotos = explode(',', $garbageTip->photos);

                foreach ($existingPhotos as $photo) {
                    $photoPath = public_path($photo);
                    if (file_exists($photoPath)) {
                        unlink($photoPath);
                    }
                }

                // Reset photo paths array since we are replacing with new uploads
                $photoPaths = [];
            }

            // Process new photo uploads
            foreach ($request->file('photos') as $imgFile) {
                $filename = Str::random(10) . '_' . time() . '.' . $imgFile->getClientOriginalExtension();
                $imagePath = 'assets/uploads/' . $filename;

                $imgFile->move(public_path('assets/uploads'), $filename);
                $photoPaths[] = $imagePath; // Add new path to array
            }
        }

        // Update the garbage tip record
        $garbageTip->update([
            'title' => $request->title,
            'photos' => implode(',', $photoPaths),
            'video' => $request->video,
            'description' => $request->description
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Garbage tip updated successfully!',
            'data' => $garbageTip
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $garbagetip = GarbageTip::find($id);

        if ($garbagetip) {
            if ($garbagetip->photos && file_exists(public_path($garbagetip->photos))) {
                unlink(public_path($garbagetip->photos));
            }
        }

        $garbagetip->delete();

        return response()->json(['message' => 'Garbage tip deleted successfully', 'type' => 'success']);
    }
}
