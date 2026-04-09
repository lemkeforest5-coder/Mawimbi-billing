<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MpesaService
{
    public function getAccessToken(): string
    {
        $baseUrl = config('mpesa.base_url');
        $key     = config('mpesa.consumer_key');
        $secret  = config('mpesa.consumer_secret');

        $response = Http::withBasicAuth($key, $secret)
            ->get($baseUrl.'/oauth/v1/generate', [
                'grant_type' => 'client_credentials',
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Failed to get M-Pesa access token: '.$response->body());
        }

        return $response->json('access_token');
    }

    public function stkPush(array $data): array
    {
        $baseUrl   = config('mpesa.base_url');
        $shortcode = config('mpesa.shortcode');
        $passkey   = config('mpesa.passkey');
        $callback  = config('mpesa.callback_url');

        $timestamp = now()->format('YmdHis');
        $password  = base64_encode($shortcode.$passkey.$timestamp);

        $phone = $this->normalizePhone($data['phone']);

        $token = $this->getAccessToken();

        $payload = [
            'BusinessShortCode' => $shortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => 'CustomerPayBillOnline',
            'Amount'            => (int) $data['amount'],
            'PartyA'            => $phone,
            'PartyB'            => $shortcode,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $callback,
            'AccountReference'  => $data['reference'],
            'TransactionDesc'   => 'Hotspot payment',
        ];

        $response = Http::withToken($token)
            ->post($baseUrl.'/mpesa/stkpush/v1/processrequest', $payload);

        if (!$response->successful()) {
            throw new \RuntimeException('STK push request failed: '.$response->body());
        }

        return $response->json();
    }

    protected function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[\s+\-]/', '', $phone);

        if (str_starts_with($cleaned, '0')) {
            return '254'.substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '+')) {
            return substr($cleaned, 1);
        }

        return $cleaned;
    }
}
