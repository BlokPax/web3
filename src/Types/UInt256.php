<?php

namespace Blokpax\Web3\Types;

use Blokpax\Web3\Contracts\Type as TypeContract;
use InvalidArgumentException;

class UInt256 implements TypeContract
{
    public function __construct(public readonly string|int $value)
    {
        if (! is_numeric($value) || bccomp($value, '0') < 0) {
            throw new InvalidArgumentException("Invalid uint256 value: {$value}");
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

        return sprintf('%064s', dechex($value));
    }

    public static function decode(string $data): mixed
    {
        return hexdec($data);
    }
}
