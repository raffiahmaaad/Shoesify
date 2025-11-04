<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class OrderStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Order $order,
        private readonly array $changes,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage())
            ->subject("Update Pesanan #{$this->order->order_number}")
            ->greeting('Halo ' . ($notifiable->name ?? 'Shoesify Member'))
            ->line("Kami punya update terbaru untuk pesanan {$this->order->order_number}.");

        foreach ($this->changes as $label => $value) {
            $message->line(Str::headline(str_replace('_', ' ', $label)) . ': ' . Str::headline((string) $value));
        }

        if ($this->order->tracking_number) {
            $message->line("Nomor pelacakan: {$this->order->tracking_number}");
        }

        return $message
            ->action('Lihat detail pesanan', url('/account/orders/' . $this->order->getKey()))
            ->line('Terima kasih telah berbelanja di Shoesify!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'changes' => $this->changes,
        ];
    }
}
