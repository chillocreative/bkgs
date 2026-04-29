<?php

namespace App\Support;

use InvalidArgumentException;

class PhoneFormatter
{
    /**
     * Normalise a Malaysian phone number to Sendora's required format `60123456789`
     * (no `+`, no leading zero, country code 60). Accepts:
     *  - 0123456789
     *  - +60123456789
     *  - 60123456789
     *  - 6 0 1 2 3 ... (with spaces / dashes)
     */
    public static function toSendora(string $input): string
    {
        $digits = preg_replace('/\D+/', '', $input);
        if ($digits === '' || $digits === null) {
            throw new InvalidArgumentException('Empty phone number.');
        }

        if (str_starts_with($digits, '60')) {
            $national = substr($digits, 2);
        } elseif (str_starts_with($digits, '0')) {
            $national = substr($digits, 1);
        } else {
            $national = $digits;
        }

        // Malaysian mobile/landline national digits: typically 9–10 long
        if (strlen($national) < 7 || strlen($national) > 11) {
            throw new InvalidArgumentException("Phone number not a valid Malaysian format: {$input}");
        }

        return '60'.$national;
    }
}
