<?php

namespace App\Http\Controllers;

use App\Models\ManageReward;
use App\Models\SubscriptionSettings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $page = "Subscription Settings";

        // Fetch rewards
        $rewards = ManageReward::all()->mapWithKeys(function ($reward) {
            return [
                $reward->reward_type => "{$reward->reward_type} - {$reward->reward_amount}"
            ];
        })->toArray(); // Convert to array

        // Fetch subscription settings
        $subscriptions = SubscriptionSettings::all(); // Retrieve all subscription data

        return view('pages.back.v_subscriptionsettings', compact('page', 'rewards', 'subscriptions'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $subscriptions = SubscriptionSettings::with('rewards')->whereNull('deleted_at')
            ->whereHas('rewards', function ($query) {
                $query->where('status', 'active');
            })->get();

        // Format the data
        $formattedData = $subscriptions->map(function ($item) {

            $rewardAmount = $item->rewards->isNotEmpty() ? $item->rewards->first()->reward_amount : 'N/A';

            return [
                'type' => $item->subscription_type,
                'description' => $item->subscription_desc,
                'reward' => $item->subscription_reward,
                'reward_amount' => $rewardAmount,
                'subscription_price' => $item->subscription_price,

                'actions' =>
                '<a class="edit-btn" href="javascript:void(0)" 
                        data-id="' . $item->id . '"
                        data-type="' . $item->subscription_type . '"
                        data-description="' . $item->subscription_desc . '"
                        data-reward="' . $item->subscription_reward . '"
                        data-price="' . $item->subscription_price . '"
                        data-modaltitle="Edit">
                    <i class="bi bi-pencil-square fs-3"></i>
                    </a>
    
                    <a class="delete-btn" href="javascript:void(0)" data-id="' . $item->id . '">
                    <i class="bi bi-trash fs-3"></i>
                    </a>'
            ];
        });

        // Return the formatted data as a JSON response
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
            'subscription_type' => 'required|string',
            'subscription_description' => 'required',
            'subscription_reward' => 'required|string',
            'subscription_price' => 'required|string'
        ]);

        SubscriptionSettings::create([
            'subscription_type' => $request->subscription_type,
            'subscription_desc' => $request->subscription_description,
            'subscription_reward' => $request->subscription_reward,
            'subscription_price' => $request->subscription_price,
        ]);

        return response()->json([
            'message' => 'Subscription saved successfully',
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

        $subscription = SubscriptionSettings::find($id);

        if (!$subscription) {
            return response()->json(['error' => 'Subscription not found'], 404);
        }

        // Validate incoming request data
        $request->validate([
            'subscription_type' => 'required|string',
            'subscription_description' => 'required',
            'subscription_reward' => 'required|string',
            'subscription_price' => 'required|string'
        ]);

        $subscription->update([
            'subscription_type' => $request->subscription_type,
            'subscription_desc' => $request->subscription_description,
            'subscription_reward' => $request->subscription_reward,
            'subscription_price' => $request->subscription_price,
        ]);

        return response()->json(['message' => 'Subscription updated successfully', 'type' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subscription = SubscriptionSettings::find($id);
        $subscription->delete();

        return response()->json(['message' => 'Subscription deleted successfully', 'type' => 'success']);
    }
}
