<?php

namespace Blokpax\Web3\Types;

use InvalidArgumentException;

class UInt256
{
    public function __construct(public readonly string|int $value)
    {
        if (! is_numeric($value) || bccomp($value, '0') < 0) {
            throw new InvalidArgumentException("Invalid uint256 value: {$value}");
        }
    }

    public static function encode(self|string|int $value): string
    {
        if ($value instanceof self) {
            $value = $value->value;
        }

        return sprintf('%064s', dechex($value));
    }

    public static function decode(string $data): string|int
    {
        return hexdec($data);
    }
}
