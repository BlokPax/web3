<?php

namespace Blokpax\Web3\Contracts;

interface Type
{
    public function value(): mixed;

    public static function encode(mixed $value): string;

    public static function decode(string $hex): mixed;
}
