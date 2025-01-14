<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\JoinEvent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::getCurrentUser();
        $page = "Event Management";
        $events = Event::whereNull('deleted_at')
            ->with(['joinEvents' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get();

        return view('pages.back.v_event', compact('page', 'events'));
    }


    public function event_ngo()
    {
        $user = User::getCurrentUser();
        $page = "Event Management";
        $events = Event::whereNull('deleted_at')
            ->with(['joinEvents' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get();

        return view('pages.back.v_eventngo', compact('page', 'events'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $events = Event::whereNull('deleted_at')->get();

        $formattedData = $events->map(function ($item) {
            return [
                'title' => $item->title,
                'description' => $item->description,
                'start_date' => $item->start_date,
                'end_date' => $item->end_date,
                'location' => $item->location,
                'time' => Carbon::parse($item->time)->format('h:i A'),
                'capacity' => $item->capacity,
                'status' => $item->status,
                'actions' =>
                '<a class="edit-btn" href="javascript:void(0)" 
                        data-id="' . $item->id . '"
                        data-title="' . $item->title . '"
                        data-description="' . $item->description . '"
                        data-startdate="' . $item->start_date . '"
                        data-enddate="' . $item->end_date . '"
                        data-location="' . $item->location . '"
                        data-time="' . $item->time . '"
                        data-capacity="' . $item->capacity . '"
                        data-status="' . $item->status . '"
                        data-modaltitle="Edit">
                    <i class="bi bi-pencil-square fs-3"></i>
                    </a>

                    <a class="delete-btn" href="javascript:void(0)" data-id="' . $item->id . '">
                    <i class="bi bi-trash fs-3"></i>
                    </a>'
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
            'title' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'location' => 'required|string',
            'capacity' => 'required|integer',
            'time' => 'required',
        ]);

        $status = $request->status ?? 'upcoming';

        Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location' => $request->location,
            'time' => $request->time,
            'capacity' => $request->capacity,
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Event saved successfully',
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
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'location' => 'required|string',
            'time' => 'required',
            'capacity' => 'required|integer',
            'status' => 'nullable|string',
        ]);

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location' => $request->location,
            'time' => $request->time,
            'capacity' => $request->capacity,
            'status' => (empty($request->status) ? 'upcoming' : $request->status)
        ]);

        return response()->json(['message' => 'Event updated successfully', 'type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::find($id);
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully', 'type' => 'success']);
    }

    public function join_event(Request $request)
    {
        $eventId = $request->input('event_id');
        $user = User::getCurrentUser();

        // Check if the user has already joined the event
        $alreadyJoined = JoinEvent::where('user_id', $user->id)
            ->where('event_id', $eventId)
            ->exists();

        if ($alreadyJoined) {
            return response()->json([
                'message' => 'You have already joined this event.',
                'type' => 'warning'
            ], 200); // 200 OK response
        }

        // Create the join event record
        $joinEvent = JoinEvent::create([
            'user_id' => $user->id,
            'event_id' => $eventId,
        ]);

        // Generate the QR code URL
        $isLocalhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
        $protocol = $isLocalhost ? 'http://' : 'https://';
        $host = $_SERVER['HTTP_HOST'];

        $scanUrl = $protocol . $host . route('event.scan', [
            'jeid' => $joinEvent->id,
            'userid' => $user->id,
            'eventid' => $eventId
        ], false); // false to avoid duplicate host in URL

        // Update the join event record with the QR code URL
        $joinEvent->update([
            'generate_qr' => $scanUrl
        ]);

        return response()->json([
            'message' => 'Joined successfully and QR code generated!',
            'type' => 'success',
            'qr_code_url' => $scanUrl
        ], 200);
    }


    public function event_attendance()
    {

        $page = "Event Attendance";

        return view('pages.back.v_eventattendance', compact('page'));
    }

    public function event_attendance_api()
    {
        $joinevents = JoinEvent::with(['user', 'event'])->get();

        $formattedData = $joinevents->map(function ($item) {
            return [
                'joinEventId' => $item->id,
                'user' => $item->user->username ?? 'N/A',
                'event' => $item->event->title ?? 'N/A',
                'generateqr' => $item->generate_qr,
                'timein' => $item->time_in,
                'timeout' => $item->time_out,
                'status' => $item->status,
                'actions' =>
                '<a class="generate-btn btn btn-primary-soft" href="javascript:void(0)" 
                        data-id="' . $item->id . '"
                        data-userid="' . $item->user_id . '"
                        data-eventid="' . $item->event_id . '">
                     Generate QR
                    </a>'
            ];
        });

        return response()->json(['data' => $formattedData]);
    }



    public function event_scan_attendance($jeid, $userid, $eventid)
    {
        $joinEvent = JoinEvent::where('id', $jeid)
            ->where('user_id', $userid)
            ->where('event_id', $eventid)
            ->first();

        if (!$joinEvent) {
            return response()->json([
                'message' => 'Invalid QR code or event data.',
                'type' => 'error',
            ], 404);
        }

        $now = Carbon::now();

        if (is_null($joinEvent->time_in)) {
            // Log time in
            $joinEvent->update(['time_in' => $now]);

            return response()->json([
                'message' => 'Time-in recorded successfully.',
                'type' => 'success',
                'time_in' => $now->toDateTimeString(),
            ]);
        } elseif (is_null($joinEvent->time_out)) {
            // Log time out
            $joinEvent->update(['time_out' => $now]);

            // After successful time-out, claim the reward (10 points)
            $joinEvent->update(['claimed_rewards' => '10']);

            return response()->json([
                'message' => 'Time-out recorded successfully. 10 points claimed.',
                'type' => 'success',
                'time_out' => $now->toDateTimeString(),
                'claimed_rewards' => '10',
            ]);
        }

        return response()->json([
            'message' => 'Attendance already completed.',
            'type' => 'info',
        ]);
    }
}
