<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

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
        //
    }
}
