<?php

namespace App\Support\OaiPmh;

final class ResumptionToken
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public static function encode(array $payload): string
    {
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $json = is_string($json) ? $json : '{}';

        $payloadB64 = self::base64UrlEncode($json);
        $signature = hash_hmac('sha256', $payloadB64, (string) config('oai.token_secret'));

        return $payloadB64.'.'.$signature;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function decode(string $token): ?array
    {
        $token = trim($token);

        if ($token === '' || ! str_contains($token, '.')) {
            return null;
        }

        [$payloadB64, $signature] = explode('.', $token, 2);

        if ($payloadB64 === '' || $signature === '') {
            return null;
        }

        $expected = hash_hmac('sha256', $payloadB64, (string) config('oai.token_secret'));

        if (! hash_equals($expected, $signature)) {
            return null;
        }

        $json = self::base64UrlDecode($payloadB64);

        if (! is_string($json)) {
            return null;
        }

        $payload = json_decode($json, true);

        if (! is_array($payload)) {
            return null;
        }

        return $payload;
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $value): ?string
    {
        if (preg_match('/^[A-Za-z0-9\-_]+$/', $value) !== 1) {
            return null;
        }

        $padded = $value.str_repeat('=', (4 - strlen($value) % 4) % 4);
        $decoded = base64_decode(strtr($padded, '-_', '+/'), true);

        return $decoded === false ? null : $decoded;
    }
}
