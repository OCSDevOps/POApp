<?php

namespace App\Services\Security;

class TotpService
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a random base32 TOTP secret.
     */
    public function generateSecret(int $length = 32): string
    {
        $alphabet = self::BASE32_ALPHABET;
        $maxIndex = strlen($alphabet) - 1;
        $secret = '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, $maxIndex)];
        }

        return $secret;
    }

    /**
     * Verify a TOTP code with a small time drift window.
     */
    public function verifyCode(string $secret, string $code, int $window = 1, int $period = 30): bool
    {
        $normalizedCode = preg_replace('/\D/', '', $code ?? '');
        if (strlen($normalizedCode) !== 6) {
            return false;
        }

        $timeSlice = (int) floor(time() / $period);
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals($this->getCodeForSlice($secret, $timeSlice + $i), $normalizedCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Build otpauth URI for authenticator apps.
     */
    public function getOtpAuthUrl(string $appName, string $accountName, string $secret): string
    {
        $issuer = rawurlencode($appName);
        $label = rawurlencode($appName . ':' . $accountName);

        return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Get current code for a given secret.
     */
    private function getCodeForSlice(string $secret, int $timeSlice): string
    {
        $key = $this->base32Decode($secret);
        $binaryTime = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $binaryTime, $key, true);

        $offset = ord(substr($hash, -1)) & 0x0F;
        $binaryCode = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        $otp = $binaryCode % 1000000;

        return str_pad((string) $otp, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Base32 decode implementation for RFC 4648 alphabet.
     */
    private function base32Decode(string $input): string
    {
        $cleanInput = strtoupper(preg_replace('/[^A-Z2-7]/', '', $input ?? ''));
        $alphabetMap = array_flip(str_split(self::BASE32_ALPHABET));
        $bits = '';
        $output = '';

        foreach (str_split($cleanInput) as $char) {
            if (!isset($alphabetMap[$char])) {
                continue;
            }

            $bits .= str_pad(decbin($alphabetMap[$char]), 5, '0', STR_PAD_LEFT);
        }

        foreach (str_split($bits, 8) as $chunk) {
            if (strlen($chunk) === 8) {
                $output .= chr(bindec($chunk));
            }
        }

        return $output;
    }
}
