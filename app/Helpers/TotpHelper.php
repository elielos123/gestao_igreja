<?php
namespace App\Helpers;

use PragmaRX\Google2FA\Google2FA;

/**
 * Wrapper for TOTP / Google Authenticator operations.
 */
class TotpHelper
{
    private static function engine(): Google2FA
    {
        return new Google2FA();
    }

    /** Generate a new 16-char Base32 secret */
    public static function generateSecret(): string
    {
        return self::engine()->generateSecretKey(16);
    }

    /**
     * Build the otpauth:// URI for a QR code.
     * @param string $email  User e-mail (account name)
     * @param string $secret Base32 secret
     */
    public static function getQRCodeUri(string $email, string $secret): string
    {
        $g2fa   = self::engine();
        $issuer = urlencode('Gestão Igreja');
        $acct   = urlencode($email);
        return "otpauth://totp/{$issuer}:{$acct}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Validate a user-supplied 6-digit code against the stored secret.
     * Allows ±1 window (30 s tolerance).
     */
    public static function verify(string $secret, string $code): bool
    {
        try {
            return (bool) self::engine()->verifyKey($secret, $code, 1);
        } catch (\Exception) {
            return false;
        }
    }
}
