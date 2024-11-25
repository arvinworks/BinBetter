<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionExpiration;
use App\Models\ManageReward;
use App\Models\SubscriptionSettings;
use App\Models\ClaimReward;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index()
    {
        $page = "Subscription";
        $subPendingCount = 0;

        $subPendingCount = Subscription::where('status', 'Pending')->count();

        return view('pages.back.v_subscription', compact('page', 'subPendingCount'));
    }

    public function subscription_api()
    {
        $user = User::getCurrentUser();

        $subscriptionSettings = SubscriptionSettings::with(['subscriptions', 'rewards'])
            ->where('deleted_at', null)
            ->get();

        $usersubscription = Subscription::with(['user', 'subscriptionSetting',  'subscriptionSetting.rewards'])
            ->where('user_id', $user->id)
            ->get();

        $formattedData = [];

        if (auth()->check() && auth()->user()->role === 'Superadmin') {
            $subscriptions = Subscription::with(['user',  'subscriptionSetting', 'subscriptionSetting.rewards'])->get();



            $formattedData = $subscriptions->map(function ($subscription) {

                $rewardAmount = optional($subscription->subscriptionSetting->rewards->first())->reward_amount ?? 'N/A';

                $username = optional($subscription->user)->username ?? 'N/A';
                $userRole = optional($subscription->user)->role ?? 'N/A';
                $userProfile = optional($subscription->user)->profile ?? 'N/A';
                $subscriptionType = optional($subscription->subscriptionSetting)->subscription_type ?? 'N/A';
                $subscriptionReward = optional($subscription->subscriptionSetting)->subscription_reward ?? 0;
                $multiplierPromotion = optional($subscription->subscriptionSetting)->multiplier_promotion ?? 0;

                return [
                    'username' => $username,
                    'role' => $userRole,
                    'profile' => $userProfile,
                    'subscription' => $subscriptionType,
                    'reward' => $subscriptionReward,
                    'reward_amount' => $rewardAmount,
                    // 'promotion' => $multiplierPromotion,
                    'status' => $subscription->status,
                    'actions' => '<button class="btn btn-outline-dark approve-btn btn-sm" ' . ($subscription->status === 'Approved' ||  $subscription->status === 'Expired' ? 'disabled' : '') . ' href="javascript:void(0)" data-id="' . $subscription->id . '">
                                    Approve <i class="bi bi-check-lg fs-5"></i>
                                  </button>'
                ];
            });
        }

        // if (auth()->check() && auth()->user()->role === 'Superadmin') {
        //     $subscriptions = Subscription::with(['user', 'subscriptionSetting.rewards'])->get();

        //     $formattedData = $subscriptions->map(function ($subscription) {
        //         // Safely access the reward_amount
        //         $rewardAmount = optional($subscription->subscriptionSetting->rewards->first())->reward_amount ?? 'N/A';

        //         $username = optional($subscription->user)->username ?? 'N/A';
        //         $userRole = optional($subscription->user)->role ?? 'N/A';
        //         $userProfile = optional($subscription->user)->profile ?? 'N/A';
        //         $subscriptionType = optional($subscription->subscriptionSetting)->subscription_type ?? 'N/A';
        //         $subscriptionReward = optional($subscription->subscriptionSetting)->subscription_reward ?? 0;
        //         $multiplierPromotion = optional($subscription->subscriptionSetting)->multiplier_promotion ?? 0;

        //         return [
        //             'username' => $username,
        //             'role' => $userRole,
        //             'profile' => $userProfile,
        //             'subscription' => $subscriptionType,
        //             'reward' => $subscriptionReward,
        //             'reward_amount' => $rewardAmount,  // Add this line to include reward_amount
        //             'promotion' => $multiplierPromotion,
        //             'status' => $subscription->status,
        //             'actions' => '<button class="btn btn-outline-dark approve-btn btn-sm" ' . ($subscription->status === 'Approved' ? 'disabled' : '') . ' href="javascript:void(0)" data-id="' . $subscription->id . '">
        //                             Approve <i class="bi bi-check-lg fs-5"></i>
        //                           </button>'
        //         ];
        //     });

        //     return response()->json(['data' => $formattedData]);
        // }


        // Return the subscription settings and formatted subscription data (if Superadmin)
        return response()->json([
            'data' => $subscriptionSettings,
            'datausersubs' => $usersubscription,
            'datasubscription' => $formattedData
        ]);
    }



    // public function store_subscription(Request $request)
    // {
    //     $user = User::getCurrentUser();
    //     $subscriptionSettingsId = $request->input('subscription_settings_id');
    //     $subscriptionType = $request->input('subscription_type');

    //     $existingSubscription = Subscription::where('user_id', $user->id)->first();

    //     // If the subscription type is not 'reselect'
    //     if ($subscriptionType !== 'reselect') {
    //         if (!$existingSubscription) {
    //             Subscription::create([
    //                 'user_id' => $user->id,
    //                 'subscription_id' => $subscriptionSettingsId,
    //                 'status' => 'Pending',
    //             ]);


    //              // add the to SubscriptionExpiration model the expiration_date

    //             return response()->json([
    //                 'message' => 'Subscription created successfully!',
    //                 'type' => 'success'
    //             ]);
    //         } else {
    //             $existingSubscription->update([
    //                 'subscription_id' => $subscriptionSettingsId,
    //                 'status' => 'Pending',
    //             ]);

    //             // add the to SubscriptionExpiration model the expiration_date
    //         }

    //         return response()->json([
    //             'message' => 'Subscription updated successfully!',
    //             'type' => 'success'
    //         ]);
    //     }

    //     if ($existingSubscription) {
    //         $existingSubscription->update(['status' => 'Pending']);

    //         return response()->json([
    //             'message' => 'Subscription reselected and status updated to Pending!',
    //             'type' => 'success'
    //         ]);
    //     }

    //     return response()->json([
    //         'message' => 'Subscription not found for reselect.',
    //         'type' => 'error'
    //     ], 404);
    // }

    public function store_subscription(Request $request)
    {
        $user = User::getCurrentUser();
        $subscriptionSettingsId = $request->input('subscription_settings_id');
        $subscriptionType = $request->input('subscription_type');

        $subscriptionSetting = SubscriptionSettings::where('id', $subscriptionSettingsId)->first();
        $getReward = ManageReward::where('reward_type', $subscriptionSetting->subscription_reward)->first();
        $existingSubscription = Subscription::where('user_id', $user->id)->first();
        $today = Carbon::today();
        $claimableDates = [];

        // If the subscription type is not 'reselect'
        if ($subscriptionType !== 'reselect') {
            if (!$existingSubscription) {
                // Create a new subscription
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'subscription_sett_id' => $subscriptionSettingsId,
                    'status' => 'Pending',
                ]);

                // Calculate the expiration date
                $expirationDate = Carbon::now();
                if ($getReward->reward_expiration_type === 'days') {
                    $expirationDate->addDays($getReward->reward_expiration_value);
                } elseif ($getReward->reward_expiration_type === 'month' || $getReward->reward_expiration_type === 'months') {
                    $expirationDate->addMonths($getReward->reward_expiration_value);
                }

                $expirationDateFormatted = $expirationDate->format('Y-m-d');

                for ($date = $today->copy(); $date->lte($expirationDateFormatted); $date->addDay()) {
                    $claimableDates[] = $date->format('Y-m-d');
                }

                // Convert claimable dates to a comma-delimited string
                $claimableDatesString = implode(',', $claimableDates);

                // Create the expiration record
                SubscriptionExpiration::create([
                    'subscription_id' => $subscription->id,
                    'expiration_date' => $expirationDateFormatted,
                    'reward_dates' => $claimableDatesString,
                ]);

                return response()->json([
                    'message' => 'Subscription created successfully!',
                    'type' => 'success'
                ]);
            } else {
                // Update existing subscription
                $existingSubscription->update([
                    'subscription_sett_id' => $subscriptionSettingsId,
                    'status' => 'Pending',
                ]);

                // Calculate and update the expiration date
                $expirationDate = Carbon::now();
                if ($getReward->reward_expiration_type === 'days') {
                    $expirationDate->addDays($getReward->reward_expiration_value);
                } elseif ($getReward->reward_expiration_type === 'month' || $getReward->reward_expiration_type === 'months') {
                    $expirationDate->addMonths($getReward->reward_expiration_value);
                }


                $expirationDateFormatted = $expirationDate->format('Y-m-d');

                for ($date = $today->copy(); $date->lte($expirationDateFormatted); $date->addDay()) {
                    $claimableDates[] = $date->format('Y-m-d');
                }

                // Convert claimable dates to a comma-delimited string
                $claimableDatesString = implode(',', $claimableDates);

                // Create the expiration record
                SubscriptionExpiration::create([
                    'subscription_id' => $existingSubscription->id,
                    'expiration_date' => $expirationDateFormatted,
                    'reward_dates' => $claimableDatesString,
                ]);

                return response()->json([
                    'message' => 'Subscription updated successfully!',
                    'type' => 'success'
                ]);
            }
        }

        // Handle the case for reselecting subscriptions if needed
        if ($existingSubscription) {
            $existingSubscription->update(['status' => 'Pending']);
            return response()->json([
                'message' => 'Subscription reselected and status updated to Pending!',
                'type' => 'success'
            ]);
        }

        return response()->json([
            'message' => 'Subscription not found for reselect.',
            'type' => 'error'
        ], 404);
    }


    public function cancel_subscription(Request $request)
    {

        $subscriptionId = $request->input('subscription_id');

        $subscription =  Subscription::find($subscriptionId);

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found.'], 400);
        }

        $subscription->update([
            'status' => 'Cancelled',
        ]);

        return response()->json([
            'message' => 'Subscription successfully cancelled!',
            'type' => 'success'
        ]);
    }


    public function approve_subscription(Request $request)
    {

        $subscriptionId = $request->input('subscription_id');

        $subscription =  Subscription::find($subscriptionId);

        if (!$subscription) {
            return response()->json(['message' => 'Subscription not found.'], 400);
        }

        $subscription->update([
            'status' => 'Approved',
        ]);

        return response()->json([
            'message' => 'Subscription successfully approved!',
            'type' => 'success'
        ]);
    }

    public function subscription_reward()
    {
        $page = "Subscription Daily Reward";
        $user = User::getCurrentUser();

        $today = Carbon::today();

        // Fetch the rewards with related subscription details
        $rewards = SubscriptionExpiration::with('subscriptions')
            ->whereHas('subscriptions', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('status', 'Approved');
            })
            ->where('status', 'To-Claim')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();

        // Flatten and parse reward dates from each subscription expiration
        $claimableDates = [];
        $claimedDates = ClaimReward::where('user_id', $user->id)
            ->whereIn('subs_expiry_id', $rewards->pluck('id'))
            ->pluck('date_claim')
            ->toArray();

        foreach ($rewards as $reward) {

            // Check if the subscription expiration date matches today's date and update statuses
            if ($today->eq(Carbon::parse($reward->expiration_date))) {
    
                Subscription::where('id', $reward->subscription_id)
                    ->update(['status' => 'Expired']);

                SubscriptionExpiration::where('id', $reward->id)
                    ->update(['status' => 'Expired']);
            }

            $dates = explode(',', $reward->reward_dates);
            foreach ($dates as $date) {
                $claimableDates[] = [
                    'date' => $date,
                    'expiration_id' => $reward->id,
                    'claimed' => in_array($date, $claimedDates) // Check if the date is already claimed
                ];
            }
        }

        return view('pages.back.v_subscriptionreward', compact('page', 'claimableDates'));
    }



    public function subscription_claim_reward(Request $request)
    {
        $user = User::getCurrentUser();
        $subsExpiryId = $request->input('subs_expiry_id');
        $dateClaim = $request->input('date_claim');

        $request->validate([
            'subs_expiry_id' => 'required|integer|exists:subscription_expirations,id',
            'date_claim' => 'required|date',
        ]);

        $subsExp = SubscriptionExpiration::find($subsExpiryId);
        if (!$subsExp) {
            return response()->json(['success' => false, 'message' => 'Invalid subscription expiration.']);
        }

        $subs = Subscription::where('id', $subsExp->subscription_id)->where('user_id', $user->id)->first();
        if (!$subs) {
            return response()->json(['success' => false, 'message' => 'Invalid subscription data.']);
        }

        $subscriptionSetting = SubscriptionSettings::find($subs->subscription_sett_id);
        if (!$subscriptionSetting) {
            return response()->json(['success' => false, 'message' => 'Subscription settings not found.']);
        }

        $getReward = ManageReward::where('reward_type', $subscriptionSetting->subscription_reward)->first();
        if (!$getReward) {
            return response()->json(['success' => false, 'message' => 'Reward information not available.']);
        }

        $existingClaim = ClaimReward::where('subs_expiry_id', $subsExpiryId)
            ->where('user_id', $user->id)
            ->where('date_claim', $dateClaim)
            ->first();

        if ($existingClaim) {
            return response()->json(['success' => false, 'message' => 'You have already claimed this reward.']);
        }

        ClaimReward::create([
            'subs_expiry_id' => $subsExpiryId,
            'user_id' => $user->id,
            'date_claim' => $dateClaim,
            'amount_claim' => $getReward->reward_amount,
        ]);

        return response()->json(['success' => true, 'message' => 'Reward claimed successfully!']);
    }
}
