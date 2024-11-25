<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCredentialsNotification extends Notification
{
    use Queueable;

    protected $username;
    protected $email;
    protected $password;
    protected $type;

    public function __construct($username, $email, $type, $password = null)
    {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password; // Password is optional for updates
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Hello! ' . $this->type)
            ->line("Your Account Credentials")
            ->line("Username: {$this->username}")
            ->line("Email: {$this->email}");

        if ($this->password) {
            $message->line("Password: {$this->password}");
        }

        $message->line('Please keep your credentials safe.')
        ->line('Change your password immediately for security purposes.');

        return $message;
    }

}
