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
    /* public function register()
    {
        $this->app->singleton('user.notifications', function () {
            $user = Auth::user();

            // Extract barangay from the user's address
            $barangay = null;
            if ($user && strpos($user->address, ',') !== false) {
                $barangay = trim(explode(',', $user->address)[1]);
            }

            $notifications = [];

            if ($barangay) {
                $scheduleQuery = DB::table('garbage_schedules')
                    ->where('barangay', $barangay)
                    ->orderBy('id', 'DESC')
                    ->limit(3);

                if ($scheduleQuery->exists()) {
                    $schedules = $scheduleQuery->get();

                    foreach ($schedules as $schedule) {
                        $notifications[] = [
                            'sender_username' => 'Garbage Schedule',
                            'text' => "Garbage collection for $schedule->barangay is on $schedule->collection_day between $schedule->time at $schedule->street.",
                        ];
                    }
                }
            }

            return $notifications;
        });
    } */

    /*  public function register()
    {
        $this->app->singleton('user.notifications', function () {
            $user = Auth::user();

            // Extract barangay from the user's address
            $barangay = null;
            if ($user && strpos($user->address, ',') !== false) {
                $barangay = trim(explode(',', $user->address)[1]);
            }

            $notifications = [];

            // Get notifications for garbage schedules based on barangay
            if ($barangay) {
                $scheduleQuery = DB::table('garbage_schedules')
                    ->where('barangay', $barangay)
                    ->orderBy('id', 'DESC')
                    ->limit(3);

                if ($scheduleQuery->exists()) {
                    $schedules = $scheduleQuery->get();

                    foreach ($schedules as $schedule) {
                        $notifications[] = [
                            'sender_username' => 'Garbage Schedule',
                            'text' => "Garbage collection for $schedule->barangay is on $schedule->collection_day between $schedule->time at $schedule->street.",
                        ];
                    }
                }
            }

            $role = $user->role;

            // Fetch notifications for users who joined events
            $joinedEvents =
                DB::table('join_events')
                ->join('events', 'join_events.event_id', '=', 'events.id')
                ->where('join_events.status', 'Approved') // Ensure 'Approved' is quoted
                ->select('join_events.user_id', 'events.title', 'events.start_date', 'events.location')
                ->get();

            foreach ($joinedEvents as $event) {
                $user = DB::table('users')->find($event->user_id);
                $notifications[] = [
                    'sender_username' => 'Event Notification',
                    'text' => "You have joined the event: {$event->title} on {$event->start_date} at {$event->location}.",
                ];
            }

            return $notifications;
        });
    } */

    public function register()
    {
        $this->app->singleton('user.notifications', function () {
            $user = Auth::user();

            // Extract barangay from the user's address
            $barangay = null;
            if ($user && strpos($user->address, ',') !== false) {
                $barangay = trim(explode(',', $user->address)[1]);
            }

            $notifications = [];

            // Get notifications for garbage schedules based on barangay
            if ($barangay) {
                $scheduleQuery = DB::table('garbage_schedules')
                    ->where('barangay', $barangay)
                    ->orderBy('id', 'DESC')
                    ->limit(3);

                if ($scheduleQuery->exists()) {
                    $schedules = $scheduleQuery->get();

                    foreach ($schedules as $schedule) {
                        $notifications[] = [
                            'sender_username' => 'Garbage Schedule',
                            'text' => "Garbage collection for $schedule->barangay is on $schedule->collection_day between $schedule->time at $schedule->street.",
                        ];
                    }
                }
            }

            $role = $user->role;
            if ($role == 'Resident') {

                $joinedEvents = DB::table('join_events')
                    ->join('events', 'join_events.event_id', '=', 'events.id')
                    ->where('join_events.status', 'Approved')
                    ->where('join_events.user_id', $user->id) // Filter by the user's ID
                    ->select('join_events.user_id', 'events.title', 'events.start_date', 'events.location')
                    ->get();

                foreach ($joinedEvents as $event) {
                    $notifications[] = [
                        'sender_username' => 'Event Notification',
                        'text' => "You have joined the event: {$event->title} on {$event->start_date} at {$event->location}.",
                    ];
                }
            } else {

                $joinedEvents = DB::table('join_events')
                    ->join('events', 'join_events.event_id', '=', 'events.id')
                    ->where('join_events.status', 'Approved')
                    ->select('join_events.user_id', 'events.title', 'events.start_date', 'events.location')
                    ->get();

                foreach ($joinedEvents as $event) {
                    $user = DB::table('users')->find($event->user_id);
                    $notifications[] = [
                        'sender_username' => 'Event Notification',
                        'text' => "{$user->username} joined the event: {$event->title} on {$event->start_date} at {$event->location}.",
                    ];
                }
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
