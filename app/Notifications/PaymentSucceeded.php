<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class PaymentSucceeded extends Notification
{
    use Queueable;

    protected Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    protected function humanPlanName(Payment $p): string
    {
        return match ((int) $p->amount) {
            10   => 'KUMI – 40 Min',
            20   => 'MBAO – 2 Hours',
            80   => 'DAILY – 24 Hours',
            280  => 'WEEKLY SOLO – 7 Days',
            720  => 'MONTHLY SOLO – 30 Days',
            4200 => 'QTRLY FAMILY x4 – 90 Days',
            default => 'Custom plan',
        };
    }

    public function via($notifiable): array
    {
        return ['telegram'];
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        $p = $this->payment;

        $lines = [
            '✅ Mawimbi Hotspot Payment',
            "Plan: {$this->humanPlanName($p)}",
            "Amount: {$p->amount}",
            "Receipt: {$p->reference}",
            "Phone: {$p->phone}",
            "Voucher: {$p->voucher_code}",
            "Router: {$p->router_id}",
            "Status: {$p->status}",
            "Time: {$p->created_at}",
        ];

        return TelegramMessage::create()
            ->to(config('services.telegram_chat_id') ?? env('TELEGRAM_CHAT_ID'))
            ->content(implode("\n", $lines));
    }
}