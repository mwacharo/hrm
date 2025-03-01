<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveApprovalNotification extends Notification
{
    use Queueable;

    protected $leave;

    public function __construct($leave)
    {
        $this->leave = $leave;
    }

    public function toMail($notifiable)
    {
        $this->leave->load('user'); // Ensure we have the related data

        $employeeName = $this->leave->user->firstname . ' ' . $this->leave->user->lastname;
        // $approverName = $this->leave->approvedBy->firstname . ' ' . $this->leave->approvedBy->lastname;

        return (new MailMessage)
            ->subject('Your Leave Request has been Approved')
            ->greeting('Hello ' . $employeeName . ',')
            ->line('We are pleased to inform you that your leave request has been approved.')
            ->line('**Leave Details:**')
            ->line('**Leave Type:** ' . $this->leave->leave_type->name)
            ->line('**Duration:** ' . $this->leave->from . ' to ' . $this->leave->to)
            ->line('**Total Leave Days:** ' . $this->leave->days)
            // ->line('**Approved By:** ' . $approverName)
            ->action('View Leave', url('/leaves/' . $this->leave->id))
            ->line('Enjoy your leave and have a great time!')
            ->line('Best regards,')
            ->line(config('app.name') . ' HR Team');
    }

    public function via($notifiable)
    {
        return ['mail'];
    }
}
