<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\UserCredentialsNotification;

class UserMngtNGOController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = "NGO's";
        return view('pages.back.v_ngo', compact('page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ngo = User::where('role', 'NGO')
        ->whereNull('deleted_at')
        ->get();

        $formattedData = $ngo->map(function ($item) {
            
              $statusBtn = $item->isDisable == 0
            ? '<a class="disable-btn" href="javascript:void(0)" data-id="' . $item->id . '">
                    <i class="bi bi-x fs-3"></i>
                </a>'
            : '<a class="enable-btn" href="javascript:void(0)" data-id="' . $item->id . '">
                    <i class="bi bi-check fs-3"></i>
                </a>';
                
            
            return [
                'profile' => $item->profile,
                'username' => $item->username,
                'email' => $item->email,
                'address' => $item->address,
                'actions' =>

                    '<a class="edit-btn" href="javascript:void(0)" 
                        data-id="' . $item->id . '"
                        data-username="' . $item->username . '"
                        data-email="' . $item->email . '"
                        data-address="' . $item->address . '"
                        data-modaltitle="Edit">
                    <i class="bi bi-pencil-square fs-3"></i>
                    </a>

                    <a class="delete-btn" href="javascript:void(0)" data-id="' . $item->id . '">
                    <i class="bi bi-trash fs-3"></i>
                    </a>'.  $statusBtn
                ];
        });

        return response()->json(['data' => $formattedData]);
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
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'min:8',
               
            ],
            'address' => 'required|string',
        ]);

        $imagePath = null;

        if ($request->hasFile('profile')) {
            $imgFile = $request->file('profile');
            $filename = time() . '_' . $imgFile->getClientOriginalName();
            $imagePath = 'assets/uploads/' . $filename;
            $imgFile->move(public_path('assets/uploads'), $filename);
        }

        $user = User::create([
            'profile' => $imagePath,
            'username' => $request->username,
            'email' => $request->email,
            'password' =>  Hash::make($request->input('password')),
            'role' => 'NGO',
            'address' => $request->address,
        ]);

        $user->notify(new UserCredentialsNotification($user->username, $user->email, 'NGO', $request->input('password')));
        

        return response()->json([
            'message' => 'NGO account saved successfully',
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
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Validate incoming request data
        $request->validate([
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => [
                'nullable', // make password optional on update
                'min:8',
            
            ],
            'address' => 'required|string',
        ]);

        $imagePath = $user->profile;

        if ($request->hasFile('profile')) {

            if ($user->profile && file_exists(public_path($user->profile))) {
                unlink(public_path($user->profile));
            }

            $imgFile = $request->file('profile');
            $filename = time() . '_' . $imgFile->getClientOriginalName();
            $imagePath = 'assets/uploads/' . $filename;
            $imgFile->move(public_path('assets/uploads'), $filename);
        }

        $user->update([
            'profile' => $imagePath,
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->input('password')) : $user->password,
            'address' => $request->address,
        ]);

        $user->notify(new UserCredentialsNotification($user->username, $user->email, 'NGO', $request->password ? $request->input('password') : null));

        return response()->json(['message' => 'NGO account updated successfully', 'type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $user = User::find($id);

        if ($user) {
            if ($user->profile && file_exists(public_path($user->profile))) {
                unlink(public_path($user->profile));
            }
        }

        $user->delete();

        return response()->json(['message' => 'NGO account deleted successfully', 'type' => 'success']);
    }
    
    
    
     public function enable(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);

        $user->update(['isDisable' => 0]);

        return response()->json(['message' => 'Account enabled successfully', 'type' => 'success']);
    }
    
    public function disable(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);

        $user->update(['isDisable' => 1]);

        return response()->json(['message' => 'Account disabled successfully', 'type' => 'success']);
    }
    
}
