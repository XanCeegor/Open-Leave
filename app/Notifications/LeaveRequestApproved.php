<?php

namespace App\Notifications;

use App\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveRequestApproved extends Notification
{
    use Queueable;
    public $leave;  //must be public for queue to work

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($leavenr)
    {
        $this->leave = Leave::where('leavenr', $leavenr)->first();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
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
            ->from(env('NOTIFICATION_EMAIL_ADDRESS'), 'Leave')
            ->subject('Leave Request Approved - ' . $this->leave->leavenr)
            ->line('Your leave request has been Approved. Click below to view more information.')
            ->action('View Leave Request', url('leave/' . $this->leave->leavenr));
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
            'leavenr' => $this->leave->leavenr,
            'duration' => $this->leave->duration,
            'updated_at' => $this->leave->updated_at,
            'status' => 'Approved',
            'type' => $this->leave->type
        ];
    }
}
