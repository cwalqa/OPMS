<?php

namespace App\Notifications;

use App\Models\QuickbooksEstimates;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;

    /**
     * Create a new notification instance.
     *
     * @param QuickbooksEstimates $order
     * @return void
     */
    public function __construct(QuickbooksEstimates $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // Notify via email and store in database
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
                    ->subject('Order Approved: ' . $this->order->purchase_order_number)
                    ->line('An order has been approved.')
                    ->line('Purchase Order Number: ' . $this->order->purchase_order_number)
                    ->line('Customer: ' . $this->order->company_name)
                    ->line('Total Amount: $' . $this->order->total_amount)
                    ->action('View Order', route('admin.viewOrderDetails', $this->order->id))
                    ->line('Thank you for your attention!');
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
            'order_id' => $this->order->id,
            'purchase_order_number' => $this->order->purchase_order_number,
            'customer_name' => $this->order->customer_name,
            'total_amount' => $this->order->total_amount,
        ];
    }
}
