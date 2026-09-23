<?php
declare(strict_types=1);

class ZarinPal
{
    private string $merchantId;
    private bool   $sandbox;
    private string $requestUrl;
    private string $verifyUrl;
    private string $startPayUrl;

    public function __construct()
    {
        $this->merchantId  = defined('ZARINPAL_MERCHANT') ? ZARINPAL_MERCHANT : '';
        $this->sandbox     = defined('ZARINPAL_SANDBOX') && ZARINPAL_SANDBOX;

        if ($this->sandbox) {
            $this->requestUrl  = 'https://sandbox.zarinpal.com/pg/v4/payment/request.json';
            $this->verifyUrl   = 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json';
            $this->startPayUrl = 'https://sandbox.zarinpal.com/pg/StartPay/';
        } else {
            $this->requestUrl  = 'https://api.zarinpal.com/pg/v4/payment/request.json';
            $this->verifyUrl   = 'https://api.zarinpal.com/pg/v4/payment/verify.json';
            $this->startPayUrl = 'https://www.zarinpal.com/pg/StartPay/';
        }
    }

    public function request(
        int    $amount,
        string $description,
        string $callbackUrl,
        string $mobile = '',
        string $email  = ''
    ): array {
        $body = [
            'merchant_id'  => $this->merchantId,
            'amount'       => $amount,
            'description'  => $description,
            'callback_url' => $callbackUrl,
        ];
        if ($mobile) $body['metadata']['mobile'] = $mobile;
        if ($email)  $body['metadata']['email']  = $email;

        $response = $this->post($this->requestUrl, $body);
        if ($response === null) {
            return ['ok' => false, 'authority' => null, 'url' => null, 'error' => 'اتصال به درگاه پرداخت ممکن نشد.'];
        }

        $data = $response['data'] ?? [];
        $errors = $response['errors'] ?? [];

        if (!empty($data['code']) && $data['code'] === 100) {
            $authority = $data['authority'];
            return [
                'ok'        => true,
                'authority' => $authority,
                'url'       => $this->startPayUrl . $authority,
                'error'     => null,
            ];
        }

        $errMsg = $errors['message'] ?? ($errors[0]['message'] ?? 'خطا در اتصال به درگاه.');
        return ['ok' => false, 'authority' => null, 'url' => null, 'error' => $errMsg];
    }

    public function verify(string $authority, int $amount): array
    {
        $body = [
            'merchant_id' => $this->merchantId,
            'authority'   => $authority,
            'amount'      => $amount,
        ];

        $response = $this->post($this->verifyUrl, $body);
        if ($response === null) {
            return ['ok' => false, 'ref_id' => null, 'error' => 'اتصال به درگاه ممکن نشد.'];
        }

        $data   = $response['data']   ?? [];
        $errors = $response['errors'] ?? [];

        if (!empty($data['code']) && in_array($data['code'], [100, 101], true)) {
            return [
                'ok'     => true,
                'ref_id' => (string)($data['ref_id'] ?? ''),
                'error'  => null,
            ];
        }

        $errMsg = $errors['message'] ?? ($errors[0]['message'] ?? 'تراکنش تایید نشد.');
        return ['ok' => false, 'ref_id' => null, 'error' => $errMsg];
    }

    private function post(string $url, array $body): ?array
    {
        $json = json_encode($body);

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST,           true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,     $json);
            curl_setopt($ch, CURLOPT_TIMEOUT,        30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
            ]);
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            $ctx    = stream_context_create([
                'http' => [
                    'method'  => 'POST',
                    'header'  => "Content-Type: application/json\r\nAccept: application/json",
                    'content' => $json,
                    'timeout' => 30,
                ],
            ]);
            $result = @file_get_contents($url, false, $ctx);
        }

        if ($result === false) return null;
        $decoded = json_decode($result, true);
        return is_array($decoded) ? $decoded : null;
    }
}
