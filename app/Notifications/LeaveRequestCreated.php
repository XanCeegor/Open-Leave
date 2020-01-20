<?php

namespace App\Notifications;

use App\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveRequestCreated extends Notification implements ShouldQueue
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
            ->line($this->leave->requested_by . ' has submitted a new ' . $this->leave->duration . ' day ' . $this->leave->type . ' request from ' . $this->leave->start_date . ' until ' . $this->leave->end_date . '.
                    Click below to view it.')
            ->action('View Leave Request', url('hr/requests/' . $this->leave->leavenr));
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
            'requested_by' => $this->leave->requested_by,
            'status' => 'New',
            'type' => $this->leave->type
        ];
    }
}
