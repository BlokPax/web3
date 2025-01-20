<?php

namespace Blokpax\Web3;

use Blokpax\Web3\Types as Types;
use InvalidArgumentException;

class Type
{
    public static function encode(mixed $value, string $type): string
    {
        return match ($type) {
            'address' => Types\Address::encode($value),
            'uint256' => Types\UInt256::encode($value),
            default   => throw new InvalidArgumentException("Unsupported type: {$type}"),
        };
    }

    public static function decode(string $hexData, array $types): mixed
    {
        $decoded = [];
        $offset = 0;
        foreach ($types as $type) {
            switch ($type) {
                case 'address':
                    $decoded[] = Types\Address::decode(substr($hexData, $offset, 64));
                    break;
                case 'uint256':
                    $decoded[] = Types\UInt256::decode(substr($hexData, $offset, 64));
                    break;
                default:
                    throw new InvalidArgumentException("Unsupported return type: {$type}");
            }
            $offset += 64;
        }

        return count($decoded) === 1 ? $decoded[0] : $decoded;

    }
}
