<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveReminderNotification extends Notification
{
    use Queueable;

    public $leave;
    public $role;

    public function __construct($leave)
    {
        $this->leave = $leave;
        // $this->role = $role;
    }

    /**
     * Determine which channels this notification should be sent to.
     */
    public function via($notifiable)
    {
        return ['mail']; // You can also add 'database', 'slack', etc.
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)


    {
        $approverFirstName = $notifiable->firstname ?: 'Approver';

        return (new MailMessage)
            ->subject('Pending Leave Approval Reminder')
            ->greeting('Hello ' . $approverFirstName . ',')
            ->line('You have a pending leave request that needs your approval.')
            ->line('Applicant Name: ' . $this->leave->user->firstname . ' ' . $this->leave->user->lastname)
            ->line('**Leave Type:** ' . $this->leave->leave_type->name)
            ->line('**Leave Status:** ' . $this->leave->status)
            ->line('**Duration:** ' . $this->leave->from . ' to ' . $this->leave->to)
            ->line('**Total Leave Days:** ' . $this->leave->days)
            // ->action('Review Leave Request', url('/leaves/' . $this->leave->id))
            ->action('Review Leave Request', url('/leave-requests'))

            ->line('Thank you for your prompt attention to this request.');
    }
}
