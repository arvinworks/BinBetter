<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PostReport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = "Post Reports";
        return view('pages.back.v_postreport', compact('page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $postGarbageReport = PostReport::where('type', 'Garbage')->whereNull('deleted_at')->get();

        $formattedGarbageData = $postGarbageReport->map(function ($item) {
            $actions = '';

            $photos = array_map('trim', explode(',', $item->photo));

            $photosHtml = '';
            foreach ($photos as $photo) {
                if (!empty($photo)) {
                    $photosHtml .= '<img src="' . e($photo) . '" alt="Garbage Tip Photo" class="avatar avatar-sm mx-1" style="width: 60px; height: 60px;">';
                } else {
                    $photosHtml .= '<img src="assets/back/images/brand/logo/noimage.jpg" style="width:100px;">';
                }
            }

            if (auth()->check()) {
                $user = auth()->user();

                if ($user->role === 'LGU') {
                    // LGU user: show status button if status is pending, otherwise show '--'
                    $actions = $item->status === 'Pending'
                        ? '<button class="status-btn btn btn-primary-soft btn-sm pt-1 pb-1" data-id="' . $item->id . '" data-type="approve">
                   Accept <i class="bi bi-check fs-5"></i>
               </button>   
               <button class="status-btn btn btn-primary-soft btn-sm pt-1 pb-1" data-id="' . $item->id . '" data-type="reject">
                  Reject <i class="bi bi-x fs-5"></i>
               </button>'
                        : '--';
                } else {
                    $residentPostreports = \App\Models\PostReport::where('resident_id', $user->id)->exists();

                    if ($residentPostreports) {
                        $actions = '<a class="edit-btn" href="javascript:void(0)" 
                           data-id="' . $item->id . '"
                           data-type="' . htmlspecialchars($item->type, ENT_QUOTES, 'UTF-8') . '"
                           data-address="' . htmlspecialchars($item->address, ENT_QUOTES, 'UTF-8') . '"
                           data-photo="' . htmlspecialchars($item->photo, ENT_QUOTES, 'UTF-8') . '"
                           data-video="' . htmlspecialchars($item->video_url, ENT_QUOTES, 'UTF-8') . '"
                           data-description="' . htmlspecialchars($item->description, ENT_QUOTES, 'UTF-8') . '"
                           data-modaltitle="Edit">
                           <i class="bi bi-pencil-square fs-3"></i>
                        </a>
                        <a class="delete-btn" href="javascript:void(0)" data-id="' . $item->id . '">
                           <i class="bi bi-trash fs-3"></i>
                        </a>';
                    } else {
                        $actions = 'Not right to access this post report';
                    }
                }
            }


            // Return formatted item data with actions
            return [
                'type' => $item->type,
                'address' => $item->address,
                'photo' => $photosHtml,
                'video' => $item->video_url,
                'description' => $item->description,
                'status' => $item->status,
                'actions' => $actions
            ];
        });


        $postRecycledReport = PostReport::where('type', 'Recycled')->whereNull('deleted_at')->get();

        $formattedRecycledData = $postRecycledReport->map(function ($item) {
            $actions = '';

            $photos = array_map('trim', explode(',', $item->photo));

            $photosHtml = '';
            foreach ($photos as $photo) {
                if (!empty($photo)) {
                    $photosHtml .= '<img src="' . e($photo) . '" alt="Garbage Tip Photo" class="avatar avatar-sm mx-1" style="width: 60px; height: 60px;">';
                } else {
                    $photosHtml .= '<img src="assets/back/images/brand/logo/noimage.jpg" style="width:100px;">';
                }
            }

            if (auth()->check()) {
                if (auth()->user()->role === 'LGU') {
                    // LGU user: show status button if status is pending, otherwise show '--'
                    $actions = $item->status === 'Pending'
                        ? '<button class="status-btn btn btn-primary-soft btn-sm pt-1 pb-1" data-id="' . $item->id . '" data-type="approve">
                           Accept <i class="bi bi-check fs-5"></i>
                       </button>   
                       
                       <button class="status-btn btn btn-primary-soft btn-sm pt-1 pb-1" data-id="' . $item->id . '" data-type="reject">
                          Reject <i class="bi bi-x fs-5"></i>
                       </button>'
                        : '--';
                } else {
                    // Non-LGU user: show edit and delete buttons
                    $actions = '<a class="edit-btn" href="javascript:void(0)" 
                               data-id="' . $item->id . '"
                               data-type="' . htmlspecialchars($item->type, ENT_QUOTES, 'UTF-8') . '"
                               data-address="' . htmlspecialchars($item->address, ENT_QUOTES, 'UTF-8') . '"
                               data-photo="' . htmlspecialchars($item->photo, ENT_QUOTES, 'UTF-8') . '"
                               data-video="' . htmlspecialchars($item->video_url, ENT_QUOTES, 'UTF-8') . '"
                               data-description="' . htmlspecialchars($item->description, ENT_QUOTES, 'UTF-8') . '"
                               data-modaltitle="Edit">
                               <i class="bi bi-pencil-square fs-3"></i>
                            </a>
                            <a class="delete-btn" href="javascript:void(0)" data-id="' . $item->id . '">
                               <i class="bi bi-trash fs-3"></i>
                            </a>';
                }
            }

            // Return formatted item data with actions
            return [
                'type' => $item->type,
                'address' => $item->address,
                'photo' => $photosHtml,
                'video' => $item->video_url,
                'description' => $item->description,
                'status' => $item->status,
                'actions' => $actions
            ];
        });

        return response()->json(['data_garbage' => $formattedGarbageData, 'data_recycled' => $formattedRecycledData]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'type' => 'required|string',
            'photo.*' => 'mimes:jpeg,png|max:10240',  // Validate each photo to be either JPG or PNG and max size of 10MB
            'video_url' => 'nullable|url|regex:/^https:\/\//', // Ensure URL starts with https
            'address' => 'required_if:type,Garbage', // Validate address only if type is Garbage
            'description' => 'required'
        ]);


        $imagePaths = []; // Array to store paths of uploaded images

        if ($request->hasFile('photo')) {
            foreach ($request->file('photo') as $imgFile) {
                $filename = time() . '_' . $imgFile->getClientOriginalName();
                $path = 'assets/uploads/' . $filename;
                $imgFile->move(public_path('assets/uploads'), $filename);
                $imagePaths[] = $path; // Add each path to the array
            }
        }

        // Convert array of paths to a comma-delimited string
        $imagePathString = implode(',', $imagePaths);

        $user = User::getCurrentUser();

        PostReport::create([
            'resident_id' => $user->id,
            'type' => $request->type,
            'photo' => $imagePathString,
            'video_url' => $request->video_url,
            'address' => $request->address,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Post report saved successfully',
            'type' => 'success'
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
        $postreport = PostReport::find($id);

        if (!$postreport) {
            return response()->json(['error' => 'Post report not found'], 404);
        }

        if ($request->type === 'Recycled') {
            $request->validate([
                'type' => 'required|string',
                // 'photo.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validate each file in the array
                'description' => 'required'
            ]);
        } else {
            $request->validate([
                'type' => 'required|string',
                // 'photo.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validate each file in the array
                'video_url' => 'nullable|url|regex:/^https:\/\//',
                'address' => 'required',
                'description' => 'required'
            ]);
        }


        // Initialize an array to store paths of uploaded images
        $imagePaths = $postreport->photo ? explode(',', $postreport->photo) : [];

        // Handle new photo uploads
        if ($request->hasFile('photo')) {
            // Delete existing photos if needed
            foreach ($imagePaths as $existingPhoto) {
                if (file_exists(public_path($existingPhoto))) {
                    unlink(public_path($existingPhoto));
                }
            }

            // Reset the image paths array for new uploads
            $imagePaths = [];

            // Loop through each uploaded file
            foreach ($request->file('photo') as $imgFile) {
                // Create a new filename
                $filename = time() . '_' . $imgFile->getClientOriginalName();
                $path = 'assets/uploads/' . $filename;
                // Move the uploaded file to the specified directory
                $imgFile->move(public_path('assets/uploads'), $filename);
                // Add the path to the array
                $imagePaths[] = $path;
            }
        }

        // Convert the array of image paths to a comma-separated string
        $imagePathString = implode(',', $imagePaths);

        // Update the post report with new values
        $postreport->update([
            'type' => $request->type,
            'photo' => $imagePathString, // This now holds the updated photo paths
            'video_url' => $request->video_url,
            'address' => $request->address,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Post report updated successfully', 'type' => 'success']);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $postreport = PostReport::find($id);

        if ($postreport) {
            if ($postreport->photo && file_exists(public_path($postreport->photo))) {
                unlink(public_path($postreport->photo));
            }
        }

        $postreport->delete();

        return response()->json(['message' => 'Post report deleted successfully', 'type' => 'success']);
    }

    public function accept_report(Request $request)
    {
        $report_id = $request->reportId;
        $status_type = $request->statusType;
        $status = '';

        if ($status_type === 'approve') {
            $status = 'Accepted';
        } else {
            $status = 'Rejected';
        }


        $postreport = PostReport::where('id', $report_id)->update(['status' => $status]);

        if ($postreport) {

            if ($status_type === 'approve') {
                return response()->json(['message' => 'Post report accepted successfully', 'type' => 'success']);
            } else {
                return response()->json(['message' => 'Post report rejected successfully', 'type' => 'success']);
            }
        } else {
            return response()->json(['message' => 'Failed to update post report status', 'type' => 'error'], 500);
        }
    }
}
