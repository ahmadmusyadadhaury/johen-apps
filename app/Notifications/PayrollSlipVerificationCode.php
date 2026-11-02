<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayrollSlipVerificationCode extends Notification
{
    use Queueable;

    public function __construct(private readonly string $code, private readonly string $period)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kode Verifikasi Slip Gaji')
            ->greeting('Halo '.$notifiable->name)
            ->line('Gunakan kode berikut untuk membuka slip gaji periode '.$this->period.':')
            ->line($this->code)
            ->line('Kode berlaku selama 5 menit dan hanya dapat digunakan untuk slip ini.')
            ->line('Jika Anda tidak meminta kode ini, abaikan email ini.');
    }
}
