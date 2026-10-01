<?php

namespace App\Support;

/**
 * An email address as it travels to the browser: encoded, never plain.
 *
 * Page props are written into the document, so a plain address in them is a plain address in the HTML that every harvester reads. The
 * address is sent as `b64:` plus its base64; the front end decodes it after the page has loaded (`useDecodedEmail`), which is when the
 * mailto link appears. Nothing here is secrecy: a script that runs JavaScript can still read it. It stops the ones that only read text.
 */
final class ObfuscatedEmail
{
    public const PREFIX = 'b64:';

    public static function encode(?string $email): string
    {
        $email = trim((string) $email);

        return $email === '' ? '' : self::PREFIX.base64_encode($email);
    }

    public static function decode(string $value): string
    {
        if (! str_starts_with($value, self::PREFIX)) {
            return $value;
        }

        $decoded = base64_decode(substr($value, strlen(self::PREFIX)), true);

        return $decoded === false ? '' : $decoded;
    }
}
