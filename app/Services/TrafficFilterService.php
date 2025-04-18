<?php

namespace App\Services;

use Illuminate\Http\Request;

class TrafficFilterService
{
    public function getCountryCode(string $ip): ?string
    {
        $url = "http://ipwho.is/{$ip}";
        $response = @file_get_contents($url);
        if (!$response) return null;

        $data = json_decode($response, true);
        return $data['success'] ?? false ? $data['country_code'] ?? null : null;
    }

    public function getLanguage(Request $request): string
    {
        return substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
    }

    public function isBlockedCountry(string $code, array $allowed): bool
    {
        return !in_array(strtoupper($code), array_map('strtoupper', $allowed));
    }

    public function isBlockedLanguage(string $lang, array $blocked): bool
    {
        return in_array(strtolower($lang), array_map('strtolower', $blocked));
    }
}
