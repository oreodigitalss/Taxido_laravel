<?php

namespace Modules\TwoFactor\SMS;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwoFactor
{
    public static function getIntent($sendTo, $message)
    {
        $apiKey = trim((string) config('twofactor.sms.twoFactor_key', ''));
        if ($apiKey === '') {
            throw new Exception('SMS gateway key is not configured.', 503);
        }

        $url = 'https://2factor.in/API/V1/'.rawurlencode($apiKey)
            .'/SMS/'.rawurlencode($sendTo).'/'.rawurlencode($message);

        try {
            $response = Http::connectTimeout(10)
                ->timeout(30)
                ->post($url);
        } catch (ConnectionException) {
            Log::warning('TwoFactor transport failed.');
            throw new Exception('SMS provider is temporarily unreachable. Please try again later.', 503);
        }

        $result = $response->json();
        if (! $response->successful() || ! is_array($result)
            || ($result['Status'] ?? null) !== 'Success'
            || ! is_string($result['Details'] ?? null) || trim($result['Details']) === '') {
            $details = is_array($result) && is_string($result['Details'] ?? null)
                ? $result['Details'] : 'Invalid provider response';
            $details = str_replace(
                [$apiKey, rawurlencode($apiKey), (string) $sendTo, rawurlencode($sendTo), (string) $message],
                '[redacted]', $details
            );
            $details = preg_replace('/https?:\/\/\S+|\b\d{6,}\b/', '[redacted]', $details);
            $details = substr(preg_replace('/[\r\n\x00-\x1F]/', ' ', $details), 0, 180);
            Log::warning('TwoFactor rejected OTP request.', [
                'http_status' => $response->status(),
                'reason' => $details,
            ]);
            throw new Exception('SMS provider rejected the OTP request. Please contact support.', 503);
        }

        return (object) $result;
    }
}
