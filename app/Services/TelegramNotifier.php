<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifier
{
    public function send(string $message): void
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        Log::info('TelegramNotifier: starting send()', [
            'has_token' => (bool) $token,
            'has_chat_id' => (bool) $chatId,
        ]);

        if (! $token || ! $chatId) {
            Log::warning('TelegramNotifier: missing TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID');
            return;
        }

        Log::info('TelegramNotifier: sending message', [
            'chat_id' => $chatId,
            'message' => $message,
        ]);

        try {
            $response = Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text'    => $message,
            ]);

            Log::info('TelegramNotifier: HTTP response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('TelegramNotifier: failed to send message', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
