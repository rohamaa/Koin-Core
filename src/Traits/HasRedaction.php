<?php

namespace Rohama\Koin\Traits;

trait HasRedaction
{
    public function sensitiveKeys(): array
    {
        return [
            'cardnumber', 'card_no', 'cardno', 'card_pan', 'cardpan', 'cardpan_enc',
            'maskedpan', 'pan', 'pin', 'cvv', 'cvv2', 'terminalkey', 'password',
            'apikey', 'username', 'user_name',
        ];
    }

    protected static function redact(array $array, array $keys): array
    {
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $array[$key] = self::redact($val, $keys);
            } else {
                if (in_array(strtolower($key), $keys)) {
                    $val = (string) $val;
                    $length = strlen($val);

                    if ($length <= 6) {
                        $array[$key] = str_repeat('*', $length);
                    } else {
                        $array[$key] = substr($val, 0, 2).str_repeat('*', $length - 4).substr($val, -2);
                    }
                }
            }
        }

        return $array;
    }
}
