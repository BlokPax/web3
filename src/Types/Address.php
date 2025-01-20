<?php

namespace Blokpax\Web3\Types;

use Blokpax\Web3\Contracts\Type as TypeContract;
use InvalidArgumentException;

class Address implements TypeContract
{
    public function __construct(public readonly string $value)
    {
        if (! preg_match('/^0x[a-fA-F0-9]{40}$/', $value)) {
            throw new InvalidArgumentException("Invalid Ethereum address: {$value}");
        }
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public static function encode(mixed $value): string
    {
        if ($value instanceof self) {
            $value = $value->value;
        }

        return sprintf('%064s', str_replace('0x', '', $value));
    }

    public static function decode(string $hex): mixed
    {
        return '0x' . substr($hex, -40);
    }
}
