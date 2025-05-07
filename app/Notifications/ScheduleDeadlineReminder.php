<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ProductionSchedule;

class ScheduleDeadlineReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $schedule;

    /**
     * Create a new notification instance.
     *
     * @param ProductionSchedule $schedule
     */
    public function __construct(ProductionSchedule $schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];  // Notify via email and store in the database
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Production Deadline Reminder')
                    ->line('A production schedule is approaching its deadline.')
                    ->line('Schedule Date: ' . $this->schedule->schedule_date)
                    ->line('Deadline Date: ' . $this->schedule->deadline_date)
                    ->action('View Schedule', url('/admin/scheduled-orders'))  // Update the URL to the actual view
                    ->line('Please take the necessary actions.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'schedule_id' => $this->schedule->id,
            'schedule_date' => $this->schedule->schedule_date,
            'deadline_date' => $this->schedule->deadline_date,
        ];
    }
}
