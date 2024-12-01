<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\PostReport;
use App\Models\PostComment;
use App\Models\Subscription;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Ramsey\Uuid\v1;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $page =  'Dashboard';
        $garbagereport = [];

        $residentCount = 0;
        $ngoCount = 0;
        $lguCount = 0;
        $subPending = 0;

        $subPending = 0;

        $id = auth()->user()->id;

        if (auth()->check() && auth()->user()->role === 'Superadmin') {

            $residentCount = User::where('role', 'Resident')->count();
            $ngoCount = User::where('role', 'NGO')->count();
            $lguCount = User::where('role', 'LGU')->count();

            $subPending = Subscription::where('status', 'Pending')->count();

            return view('pages.back.v_home', compact('page', 'garbagereport', 'residentCount', 'ngoCount', 'lguCount', 'subPending'));
        } elseif (auth()->check() && auth()->user()->role === 'LGU') {
            return view('pages.back.v_home', compact('page', 'garbagereport', 'residentCount', 'ngoCount', 'lguCount', 'subPending'));
        } elseif (auth()->check() && auth()->user()->role === 'NGO') {
            return view('pages.back.v_home', compact('page', 'garbagereport', 'residentCount', 'ngoCount', 'lguCount', 'subPending'));
        } elseif (auth()->check() && auth()->user()->role === 'Resident') {
            $garbagereport = PostReport::where('type', 'Garbage')->whereNull('deleted_at')->orderBy('id', 'DESC')->get();
            return view('pages.back.v_home', compact('page', 'garbagereport', 'residentCount', 'ngoCount', 'lguCount', 'subPending'));
        }
    }

    public function post_report_api()
    {
        $posts_garbage = PostReport::with(['resident', 'postcomments.resident', 'postcomments.replies', 'postcomments.replies.resident'])
            ->where('type', 'Garbage')
            ->whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->get();

        if ($posts_garbage) {
            return response()->json(['posts_garbage' => $posts_garbage]);
        } else {
            return response()->json(['error' => 'Post garbage not found'], 404);
        }
    }

    public function post_recycled_api()
    {
        $posts_recycled = PostReport::with(['resident',  'postcomments.resident', 'postcomments.replies', 'postcomments.replies.resident'])
            ->where('type', 'Recycled')
            ->whereNull('deleted_at')
            ->orderBy('id', 'DESC')
            ->get();

        if ($posts_recycled) {
            return response()->json(['posts_recycled' => $posts_recycled]);
        } else {
            return response()->json(['error' => 'Post recycled not found'], 404);
        }
    }

    public function post_destroy(Request $request)
    {
        $id = $request->commentid;
        $post = PostComment::find($id);
        $post->delete();

        return response()->json(['message' => 'Comment deleted successfully', 'type' => 'success']);
    }

    public function analytics_index()
    {
        $page = 'Analytics';


        // Get cleaned and uncleaned addresses with necessary details
        $cleanedAddresses = PostReport::whereNull('deleted_at')
            ->where('status', 'Accepted')
            ->get(['id', 'address', 'status']);

        $uncleanedAddresses = PostReport::whereNull('deleted_at')
            ->where('status', 'Pending')
            ->get(['id', 'address', 'status']);

        $ongoingEvents = Event::whereNull('deleted_at')
            ->where('status', 'ongoing')
            ->withCount('joinEvents')
            ->get();

        $registeredUsers = User::whereNull('deleted_at')
            ->selectRaw('DATE(created_at) as date, count(*) as count, GROUP_CONCAT(username) as names')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $registeredFemales = User::whereNull('deleted_at')
            ->where('gender', 'Female')
            ->selectRaw('DATE(created_at) as date, count(*) as count, GROUP_CONCAT(username) as names')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Registered male users count (with usernames)
        $registeredMales = User::whereNull('deleted_at')
            ->where('gender', 'Male')
            ->selectRaw('DATE(created_at) as date, count(*) as count, GROUP_CONCAT(username) as names')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Registered minor users count (with usernames)
        $registeredMinors = User::whereNull('deleted_at')
            ->where('age', '<', 18)
            ->selectRaw('DATE(created_at) as date, count(*) as count, GROUP_CONCAT(username) as names')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Registered senior users count (with usernames)
        $registeredSeniors = User::whereNull('deleted_at')
            ->where('age', '>=', 60)
            ->selectRaw('DATE(created_at) as date, count(*) as count, GROUP_CONCAT(username) as names')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();


        return view('pages.back.v_analytics', compact('page', 'ongoingEvents', 'registeredMinors', 'registeredSeniors', 'registeredMales', 'registeredFemales', 'registeredUsers', 'cleanedAddresses', 'uncleanedAddresses'));
    }

    public function view_notification()
    {
        $page = 'Notifications';
        return view('pages.back.v_notifications', compact('page'));
    }

    public function view_notification_api()
    {

        $id = auth()->user()->id;

        $messages = Message::where('recipient_id', $id)
            ->orderBy('id', 'DESC')
            ->leftJoin('users as senders', 'senders.id', '=', 'messages.sender_id')
            ->limit(3)
            ->select('messages.id', 'messages.text', 'messages.created_at', 'senders.username as sender_username')->get();

        $formattedData = $messages->map(function ($item) {
            return [
                'sender' =>  $item->sender_username,
                'message' => $item->text
            ];
        });

        return response()->json(['data' => $formattedData]);
    }
    public function approvePost(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:post_reports,id',
        ]);

        $post = PostReport::find($request->post_id);

        if ($post) {
            $post->status = 'Accepted'; // Change the status to "Accepted"
            $post->save();

            return response()->json(['message' => 'Post status updated successfully', 'type' => 'success']);
        }

        return response()->json(['message' => 'Post not found', 'type' => 'error'], 404);
    }

    public function rejectedPost(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:post_reports,id',
        ]);

        $post = PostReport::find($request->post_id);

        if ($post) {
            $post->status = 'Rejected'; // Change the status to "Accepted"
            $post->save();

            return response()->json(['message' => 'Post status updated successfully', 'type' => 'success']);
        }

        return response()->json(['message' => 'Post not found', 'type' => 'error'], 404);
    }
}
