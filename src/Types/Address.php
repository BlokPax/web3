<?php

namespace Blokpax\Web3\Types;

use InvalidArgumentException;

class Address
{
    public function __construct(public readonly string $value)
    {
        if (! preg_match('/^0x[a-fA-F0-9]{40}$/', $value)) {
            throw new InvalidArgumentException("Invalid Ethereum address: {$value}");
        }
    }

    public static function encode(string|self $value): string
    {
        if ($value instanceof self) {
            $value = $value->value;
        }

        return sprintf('%064s', str_replace('0x', '', $value));
    }

    public static function decode(string $hex): string
    {
        return '0x' . substr($hex, -40);
    }
}
