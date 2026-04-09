<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;

class TelegramAdmin
{
    use Notifiable;

    public function routeNotificationForTelegram(): string
    {
        return config('services.telegram_chat_id') ?? env('TELEGRAM_CHAT_ID');
    }
}
