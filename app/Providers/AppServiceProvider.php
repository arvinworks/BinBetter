<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('user.notifications', function () {
            $id = Auth::id();

            $notifications = [];

            $messageQuery = Message::where('recipient_id', $id)
                ->orderBy('id', 'DESC')
                ->leftJoin('users as senders', 'senders.id', '=', 'messages.sender_id')
                ->limit(3)
                ->select('messages.id', 'messages.text', 'messages.created_at', 'senders.username as sender_username');

            if ($messageQuery->exists()) {
                $notifications = $messageQuery->get();
            }

            return $notifications;
        });
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot()
    {
        View::composer('layouts.back.header', function ($view) {
            $userId = auth()->id();

            // Calculate total claimed_rewards from join_events
            $totalClaimedRewards = DB::table('join_events')
                ->where('user_id', $userId)
                ->sum(DB::raw("CAST(claimed_rewards AS UNSIGNED)"));

            // Calculate total amount_claim from claim_rewards
            $totalAmountClaim = DB::table('claim_rewards')
                ->where('user_id', $userId)
                ->sum('amount_claim');

            // Calculate total points (sum of claimed rewards and amount claims)
            $totalPoints = $totalClaimedRewards + $totalAmountClaim;

            // Pass data to the view
            $view->with(compact('totalClaimedRewards', 'totalAmountClaim', 'totalPoints'));
        });
    }
}
