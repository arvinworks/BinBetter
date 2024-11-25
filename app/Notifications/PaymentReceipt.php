<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReceipt extends Notification
{
    use Queueable;

    protected $payment;

    /**
     * Create a new notification instance.
     *
     * @param  Donation  $donation
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
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
        return (new MailMessage)
                    ->subject('Payment Receipt')
                    ->greeting('Hello ' . $notifiable->name)
                    ->line('Thank you for your payment!')
                    ->line('Amount: PHP ' . number_format($this->payment->amount, 2))
                    ->line('Date: ' . $this->payment->created_at->toFormattedDateString())
                    ->line('We appreciate your support!');
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
            'amount' => $this->payment->amount,
            'date' => $this->payment->created_at->toFormattedDateString(),
        ];
    }
}
