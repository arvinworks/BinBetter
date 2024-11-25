<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Messages';
      

        return view('pages.back.v_messages', compact('page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = User::getCurrentUser();

        if (in_array($user->role, ['LGU', 'NGO', 'Resident'])) {


            // Fetch all non-Superadmin users with their latest message and timestamp
            $chatlists = User::where('role', '<>', 'Superadmin') // Exclude Superadmin from the list
                ->where('users.id', '<>', $user->id) // Exclude the logged-in user from the list
                ->leftJoin('messages', function ($join) {
                    $join->on('users.id', '=', 'messages.recipient_id')
                        ->orOn('users.id', '=', 'messages.sender_id');
                })
                ->select('users.id', 'users.username', 'users.email', 'users.profile', 'users.role', 'users.isLogin')
                ->addSelect(DB::raw('(
                            SELECT text 
                            FROM messages 
                            WHERE (messages.recipient_id = users.id OR messages.sender_id = users.id)
                            ORDER BY created_at DESC 
                            LIMIT 1
                        ) as latest_message_text'))
                ->addSelect(DB::raw('(
                            SELECT MAX(created_at) 
                            FROM messages 
                            WHERE (messages.recipient_id = users.id OR messages.sender_id = users.id)
                        ) as latest_message_created_at'))
                ->groupBy('users.id', 'users.username', 'users.email', 'users.profile', 'users.role', 'users.isLogin')
                ->get();
        }

        return response()->json(['chatlists' => $chatlists]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'recipient_id' => 'required|integer|exists:users,id',
            'text' => 'required|string|max:255',
        ]);

        // Get the current logged-in user's ID
        $senderId = Auth::id();

        // Create a new message record
        $message = new Message();
        $message->sender_id = $senderId;
        $message->recipient_id = $validatedData['recipient_id'];
        $message->text = $validatedData['text'];
        $message->save();

        // Return a JSON response with the saved message
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($chatId)
    {
        $userId = Auth::id(); // Get current logged-in user ID

        // Fetch messages where sender_id or recipient_id matches
        $messages = Message::where(function ($query) use ($userId, $chatId) {
            $query->where('sender_id', $userId)
                ->where('recipient_id', $chatId);
        })->orWhere(function ($query) use ($userId, $chatId) {
            $query->where('sender_id', $chatId)
                ->where('recipient_id', $userId);
        })->get();

        return response()->json(['messages' => $messages]);
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
        $message = Message::find($id);
        $message->delete();

        return response()->json(['message' => 'Message  deleted successfully', 'type' => 'success']);
    }
}
