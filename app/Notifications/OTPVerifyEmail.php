<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class OTPVerifyEmail extends Notification
{
    use Queueable;

    protected $otpCode;

    /**
     * Create a new notification instance.
     *
     * @param  string  $otpCode
     * @return void
     */
    public function __construct($otpCode)
    {
        $this->otpCode = $otpCode;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // HTML content with inline styles
        $otpHtml = new HtmlString(
            '<p style="font-size: 64px; font-weight: bold; text-align: center;">' . $this->otpCode . '</p>'
        );

        return (new MailMessage)
            ->line('Here is your OTP code:')
            ->line($otpHtml)
            ->line('This code is valid for 10 minutes.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'otp_code' => $this->otpCode,
        ];
    }
}
