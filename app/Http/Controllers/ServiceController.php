<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = "Services";
        return view('pages.back.v_service', compact('page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $services = Service::whereNull('deleted_at')->get();

        $formattedData = $services->map(function ($item) {
            return [
                'service' => $item->service_type,
                'points' => $item->service_points,
                'description' => $item->description,
                'actions' =>
                    '<a class="edit-btn" href="javascript:void(0)" 
                        data-id="' . $item->id . '"
                        data-service="' . $item->service_type . '"
                        data-points="' . $item->service_points . '"
                        data-description="' . $item->description . '"
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
            'service' => 'required|string',
            'points' => 'required|integer',
            'description' => 'required'
        ]);

        Service::create([
            'service_type' => $request->service,
            'service_points' => $request->points,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Service saved successfully',
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
        $service = Service::find($id);

        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        // Validate incoming request data
        $request->validate([
            'service' => 'required|string',
            'points' => 'required|integer',
            'description' => 'required',
        ]);

        $service->update([
            'service_type' => $request->service,
            'service_points' => $request->points,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Service updated successfully', 'type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $service = Service::find($id);
        $service->delete();

        return response()->json(['message' => 'Service deleted successfully', 'type' => 'success']);
    }
}
