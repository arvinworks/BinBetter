<?php

namespace App\Http\Controllers;

use App\Models\ManageReward;
use Illuminate\Http\Request;

class RewardManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = "Reward Managment";
        return view('pages.back.v_managereward', compact('page'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $rewards = ManageReward::whereNull('deleted_at')
        ->get();

        $formattedData = $rewards->map(function ($item) {
            return [
                'rewardtype' => $item->reward_type,
                'rewardamount' => $item->reward_amount,
                'rewardexpirationvalue' => $item->reward_expiration_value,
                'rewardexpirationtype' => $item->reward_expiration_type,
                'status' => $item->status,
                'actions' =>

                    '<a class="edit-btn" href="javascript:void(0)" 
                        data-id="' . $item->id . '"
                        data-rewardtype="' . $item->reward_type . '"
                        data-rewardamount="' . $item->reward_amount . '"
                        data-rewardexpirationvalue="' . $item->reward_expiration_value . '"
                        data-rewardexpirationtype="' . $item->reward_expiration_type . '"
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
            'reward_type' => 'required|string|max:255|unique:manage_rewards,reward_type',
            'reward_amount' => 'required|integer',
            'reward_expiration_value' => 'required|integer',
            'reward_expiration_type' => 'required|string',
            'status' => 'required|string',
        ]);


        $reward = ManageReward::create([
            'reward_type' => $request->reward_type,
            'reward_amount' => $request->reward_amount,
            'reward_expiration_value' => $request->reward_expiration_value,
            'reward_expiration_type' => $request->reward_expiration_type,
            'status' => $request->status
        ]);

       
        return response()->json([
            'message' => 'Reward saved successfully',
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
        $request->validate([
            'reward_type' => 'required|string|max:255|unique:manage_rewards,reward_type,' . $id,
            'reward_amount' => 'required|integer',
            'reward_expiration_value' => 'required|integer',
            'reward_expiration_type' => 'required|string',
            'status' => 'required|string',
        ]);

        $reward = ManageReward::find($id);

        if ($reward) {
            $reward->update([
                'reward_type' => $request->reward_type,
                'reward_amount' => $request->reward_amount,
                'reward_expiration_value' => $request->reward_expiration_value,
                'reward_expiration_type' => $request->reward_expiration_type,
                'status' => $request->status
            ]);

            return response()->json([
                'message' => 'Reward updated successfully',
                'type' => 'success'
            ]);
        } else {
            return response()->json([
                'message' => 'Reward not found',
                'type' => 'error'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $reward = ManageReward::find($id);

        if ($reward) {
            $reward->delete();

            return response()->json([
                'message' => 'Reward deleted successfully',
                'type' => 'success'
            ]);
        } else {
            return response()->json([
                'message' => 'Reward not found',
                'type' => 'error'
            ], 404);
        }
    }
}
