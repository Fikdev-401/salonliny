<?php

if (!function_exists('build_landing_wa_url')) {
    /**
     * Build a WhatsApp share URL with pre-filled message.
     *
     * @param  string|null  $phone   Phone in 08xx or 62xx format. Null/empty returns null.
     * @param  string       $message Message to pre-fill (will be rawurlencoded).
     * @return string|null
     */
    function build_landing_wa_url(?string $phone, string $message = ''): ?string
    {
        $phone = normalize_wa_phone($phone);
        if (!$phone) {
            return null;
        }
        $url = 'https://wa.me/' . $phone;
        if ($message !== '') {
            $url .= '?text=' . rawurlencode($message);
        }
        return $url;
    }
}

if (!function_exists('normalize_wa_phone')) {
    /**
     * Normalize Indonesian phone number to international 62xx format.
     */
    function normalize_wa_phone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }
        if (str_starts_with($digits, '62')) {
            return $digits;
        }
        // If it starts with 8 (typical Indonesian mobile without leading 0), prefix 62
        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }
        return $digits;
    }
}
