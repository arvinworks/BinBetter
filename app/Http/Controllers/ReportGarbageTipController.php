<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GarbageTip;
use App\Models\ReportGarbageTip;
use Illuminate\Http\Request;

class ReportGarbageTipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = "Garbage Tips Reported Post";
        return view('pages.back.v_garbagetipsreports', compact('page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $garbagetip_reports = GarbageTip::with('garbage_reports')->get();

        $formattedData = $garbagetip_reports->map(function ($item) {
            // Get all report IDs and statuses
            $reportIds = $item->garbage_reports->pluck('id')->toArray();
            $reportStatuses = $item->garbage_reports->pluck('report_status')->toArray();

            // Check if any report status is equal to 1
            $isDisabled = in_array(1, $reportStatuses) ? 'disabled' : '';

            $reportsData = $item->garbage_reports->map(function ($report) {
                return [
                    'username' => $report->user->username ?? 'N/A',
                    'report_message' => $report->report_message ?? 'N/A',
                    'report_type' => $report->report_type ?? 'N/A'
                ];
            });

            return [
                'title' => e($item->title ?? 'N/A'),
                'description' => $item->description,
                'reportcount' => $item->report_count,
                'actions' => '

                 <button type="button" class="btn btn-primary-soft btn-sm show-btn" 
                    data-reports=\'' . json_encode($reportsData) . '\' 
                    data-modaltitle="Show Report">SHOW REPORT <i class="bi bi-eye fs-3"></i></button>
                    
                <button type="button" class="btn btn-primary-soft btn-sm approve-btn" 
                    ' . $isDisabled . '
                    data-id="' . implode(', ', $reportIds) . '" 
                    data-modaltitle="Approve Report">APPROVE <i class="bi bi-check fs-3"></i></button>
            '
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

        $request->validate([
            'content_id' => 'required|integer',
            'report_type' => 'required|string|max:255',
            'report_message' => 'required|string'
        ]);

        $user = User::getCurrentUser();

        $report = ReportGarbageTip::create([
            'user_id' => $user->id,
            'garbage_tip_id' => $request->content_id,
            'report_type' => $request->report_type,
            'report_message' => $request->report_message
        ]);

        if ($report) {
            $gtReportCount = GarbageTip::where('id', $request->content_id)
                ->first();

            $gtReportCount->increment('report_count');
        }

        return response()->json([
            'type' => 'success',
            'message' => 'Report send successfully!',
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
    public function destroy($ids)
    {
        $idsArray = explode(',', $ids);

        ReportGarbageTip::whereIn('id', $idsArray)->update(['report_status' => 1]);

        return response()->json([
            'message' => 'Garbage Tip Content removed successfully',
            'type' => 'success'
        ]);
    }
}
