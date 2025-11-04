<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order;
        $totalCents = $order->grand_total ?: $order->subtotal;
        $total = $totalCents ? number_format($totalCents / 100, 2) : number_format($order->grand_total ?? 0, 0);

        return (new MailMessage())
            ->subject("Pesanan #{$order->order_number} berhasil dibuat")
            ->greeting('Halo ' . ($notifiable->name ?? 'Shoesify Member'))
            ->line('Terima kasih telah mempercayakan pengalaman sneaker kamu bersama Shoesify!')
            ->line("Nomor pesanan: {$order->order_number}")
            ->line('Status awal: ' . Str::headline($order->status ?? 'pending'))
            ->line('Total pembayaran: $' . $total)
            ->action('Lihat detail pesanan', url('/dashboard'))
            ->line('Kami akan mengirim notifikasi ketika pesananmu bergerak ke tahap berikutnya.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'status' => $this->order->status,
            'total' => $this->order->grand_total,
        ];
    }
}
