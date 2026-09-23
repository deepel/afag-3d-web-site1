<?php
declare(strict_types=1);
/**
 * afag3d — ZarinPal Payment Helper
 */
class ZarinPal
{
    private static function requestUrl(): string
    {
        return ZARINPAL_SANDBOX
            ? 'https://sandbox.zarinpal.com/pg/v4/payment/request.json'
            : 'https://payment.zarinpal.com/pg/v4/payment/request.json';
    }

    private static function verifyUrl(): string
    {
        return ZARINPAL_SANDBOX
            ? 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json'
            : 'https://payment.zarinpal.com/pg/v4/payment/verify.json';
    }

    public static function payUrl(string $authority): string
    {
        return ZARINPAL_SANDBOX
            ? 'https://sandbox.zarinpal.com/pg/StartPay/' . $authority
            : 'https://www.zarinpal.com/pg/StartPay/' . $authority;
    }

    private static function post(string $url, array $data): array
    {
        $body = json_encode($data);
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json', 'Accept: application/json'],
                CURLOPT_TIMEOUT        => 20,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            $resp = curl_exec($ch);
            $err  = curl_error($ch);
            curl_close($ch);
            if ($err) return ['error' => $err];
        } else {
            $ctx = stream_context_create(['http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\nAccept: application/json\r\n",
                'content' => $body,
                'timeout' => 20,
            ]]);
            $resp = @file_get_contents($url, false, $ctx);
            if ($resp === false) return ['error' => 'خطا در اتصال به درگاه پرداخت.'];
        }
        $decoded = json_decode($resp, true);
        return is_array($decoded) ? $decoded : ['error' => 'پاسخ نامعتبر از درگاه.'];
    }

    /**
     * Request a payment authority from ZarinPal.
     * Returns ['authority' => '...', 'url' => '...'] or ['error' => '...']
     */
    public static function request(
        int    $amount,
        string $description,
        string $callbackUrl,
        string $mobile = '',
        string $email  = ''
    ): array {
        $payload = [
            'merchant_id'  => ZARINPAL_MERCHANT,
            'amount'       => $amount,
            'description'  => $description,
            'callback_url' => $callbackUrl,
        ];
        if ($mobile) $payload['metadata']['mobile'] = $mobile;
        if ($email)  $payload['metadata']['email']  = $email;

        $resp = self::post(self::requestUrl(), $payload);

        if (!empty($resp['errors'])) {
            $msg = is_array($resp['errors']) ? json_encode($resp['errors'], JSON_UNESCAPED_UNICODE) : $resp['errors'];
            return ['error' => $msg];
        }
        if (empty($resp['data']['authority'])) {
            return ['error' => 'authority دریافت نشد. ' . json_encode($resp, JSON_UNESCAPED_UNICODE)];
        }

        $authority = $resp['data']['authority'];
        return [
            'authority' => $authority,
            'url'       => self::payUrl($authority),
        ];
    }

    /**
     * Verify a payment after callback.
     * Returns ['success' => bool, 'ref_id' => string, 'card_pan' => string, 'error' => string]
     */
    public static function verify(string $authority, int $amount): array
    {
        $resp = self::post(self::verifyUrl(), [
            'merchant_id' => ZARINPAL_MERCHANT,
            'amount'      => $amount,
            'authority'   => $authority,
        ]);

        if (!empty($resp['errors'])) {
            $msg = is_array($resp['errors']) ? json_encode($resp['errors'], JSON_UNESCAPED_UNICODE) : (string)$resp['errors'];
            return ['success' => false, 'ref_id' => '', 'card_pan' => '', 'error' => $msg];
        }

        $code = (int)($resp['data']['code'] ?? -1);
        if ($code === 100 || $code === 101) {
            return [
                'success'  => true,
                'ref_id'   => (string)($resp['data']['ref_id']   ?? ''),
                'card_pan' => (string)($resp['data']['card_pan'] ?? ''),
                'error'    => '',
            ];
        }

        return [
            'success'  => false,
            'ref_id'   => '',
            'card_pan' => '',
            'error'    => 'کد خطا: ' . $code,
        ];
    }
}
