<?php

namespace Modules\TwoFactor\SMS;

use Exception;
use Illuminate\Support\Facades\Log;

class TwoFactor
{
    public static function getIntent($sendTo, $message)
    {
        $apiKey = trim((string) env('TWOFACTOR_KEY', ''));
        $templateName = trim((string) env('OTP_TEMPLATE_NAME', ''));
        if ($apiKey === '' || $templateName === '') {
            throw new Exception('SMS gateway key or OTP template is not configured.', 503);
        }

        $url = 'https://2factor.in/API/V1/' . rawurlencode($apiKey)
            . '/SMS/' . rawurlencode($sendTo) . '/' . rawurlencode($message)
            . '/' . rawurlencode($templateName);
        $ch = curl_init();
        if ($ch === false) {
            throw new Exception('SMS gateway connection could not be initialized.', 503);
        }
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ]);
        $response = curl_exec($ch);
        $curlError = curl_errno($ch);
        $httpStatus = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $curlError !== 0) {
            Log::warning('TwoFactor transport failed.', ['curl_errno' => $curlError]);
            throw new Exception('SMS provider is temporarily unreachable. Please try again later.', 503);
        }

        $result = json_decode($response);
        if ($httpStatus < 200 || $httpStatus >= 300 || !is_object($result)
            || ($result->Status ?? null) !== 'Success'
            || !is_string($result->Details ?? null) || trim($result->Details) === '') {
            $details = is_object($result) && is_string($result->Details ?? null)
                ? $result->Details : 'Invalid provider response';
            $details = str_replace(
                [$apiKey, rawurlencode($apiKey), (string) $sendTo, rawurlencode($sendTo), (string) $message],
                '[redacted]', $details
            );
            $details = preg_replace('/https?:\/\/\S+|\b\d{6,}\b/', '[redacted]', $details);
            $details = substr(preg_replace('/[\r\n\x00-\x1F]/', ' ', $details), 0, 180);
            Log::warning('TwoFactor rejected OTP request.', [
                'http_status' => $httpStatus,
                'reason' => $details,
            ]);
            throw new Exception('SMS provider rejected the OTP request. Please contact support.', 503);
        }

        return $result;
    }
}
