<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequisitionCanceledNotification extends Notification
{
    use Queueable;

    protected $requisition; // Holds the requisition instance
    protected $reason;      // Holds the cancellation reason (optional)

    /**
     * Create a new notification instance.
     */
    public function __construct($requisition, $reason = null)
    {
        $this->requisition = $requisition;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('Requisition Canceled')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We regret to inform you that your requisition (ID: ' . $this->requisition->id . ') has been canceled.');

        if ($this->reason) {
            $mailMessage->line('Reason for cancellation: ' . $this->reason);
        }

        $mailMessage->line('If you have any questions, please contact the relevant department.')
            ->action('View Requisitions', url('/requisitions')) 
            ->line('Thank you for using our application!');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'requisition_id' => $this->requisition->id,
            'status' => 'Canceled',
            'reason' => $this->reason,
        ];
    }
}
