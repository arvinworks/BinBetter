<?php

namespace App\Http\Controllers;

use App\Models\GarbageSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GarbageCollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = "Garbage Collection Schedule";
        $schedules = GarbageSchedule::whereNull('deleted_at')->get();

        return view('pages.back.v_garbagecollection', compact('page','schedules'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $garbageSchedules = GarbageSchedule::whereNull('deleted_at')->get();

        $formattedData = $garbageSchedules->map(function ($item) {

            // Default time values
            $time = ['00:00', '00:00'];

            // Check if the time exists and contains a dash to split the range
            if (!empty($item->time) && strpos($item->time, '-') !== false) {
                $time = explode('-', $item->time);
            }

            // Trim spaces to avoid issues with Carbon parsing
            $timeFrom = isset($time[0]) ? trim($time[0]) : '00:00';
            $timeTo = isset($time[1]) ? trim($time[1]) : '00:00';

            try {
                // Try parsing with Carbon and handle potential parsing issues
                $timeFromFormatted = Carbon::createFromFormat('H:i', $timeFrom)->format('g:i A');
                $timeToFormatted = Carbon::createFromFormat('H:i', $timeTo)->format('g:i A');
            } catch (\Exception $e) {
                // In case of invalid time, fallback to default times
                $timeFromFormatted = 'Invalid time';
                $timeToFormatted = 'Invalid time';
            }

            return [
                'street' => e($item->street),  // XSS Protection: The e() function ensures that any potentially dangerous content in street, barangay, collection_day, etc., is properly escaped.
                'barangay' => e($item->barangay),
                'time' => $timeFromFormatted . ' - ' . $timeToFormatted, // Formatted time with AM/PM
                'collection_day' => e($item->collection_day),
                'actions' =>
                '<a class="edit-btn" href="javascript:void(0)" 
                    data-id="' . e($item->id) . '"
                    data-street="' . e($item->street) . '"
                    data-barangay="' . e($item->barangay) . '"
                    data-timefrom="' . e($timeFrom) . '"
                    data-timeto="' . e($timeTo) . '"
                    data-day="' . e($item->collection_day) . '"
                    data-modaltitle="Edit">
                    <i class="bi bi-pencil-square fs-3"></i>
                </a>

                <a class="delete-btn" href="javascript:void(0)" data-id="' . e($item->id) . '">
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
            'street' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'timefrom' => 'required|date_format:H:i',
            'timeto' => 'required|date_format:H:i',
            'collection_day' => 'required|string',
        ]);


        GarbageSchedule::updateOrCreate(
            ['id' => $request->id],
            [
                'street' => $request->street,
                'barangay' => $request->address,
                'time' => $request->timefrom . ' - ' . $request->timeto,
                'collection_day' => $request->collection_day,
            ]
        );

        // Return success response
        return response()->json([
            'type' => 'success',
            'message' => 'Garbage schedule saved successfully!'
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
            'street' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'timefrom' => 'required|date_format:H:i',
            'timeto' => 'required|date_format:H:i',
            'day' => 'required|string',
        ]);
    
        $garbageSchedule = GarbageSchedule::findOrFail($id);

    
        $garbageSchedule->update([
            'street' => $request->street,
            'barangay' => $request->address,
            'time' => $request->timefrom . ' - ' . $request->timeto,
            'collection_day' => $request->collection_day
        ]);

        return response()->json([
            'type' => 'success',
            'message' => 'Garbage schedule updated successfully!'
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
        $garbage = GarbageSchedule::findOrFail($id);
        $garbage->delete();

        return response()->json(['message' => 'Garbage schedule deleted successfully', 'type' => 'success']);
    }
}
