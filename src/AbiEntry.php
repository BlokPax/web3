<?php

namespace Blokpax\Web3;

class AbiEntry
{
    public function __construct(protected array $entry) {}

    public function encode($params): string
    {
        $selector = static::encodeSelector($this->entry);
        $encodedParams = static::encodeParams($this->entry['inputs'], $params);

        return sprintf('0x%s',
            substr($selector, 0, 8)
            . implode('', $encodedParams)
        );
    }

    public static function encodeSelector(array $entry): string
    {
        return Abi::hash(
            $entry['name']
            . '('
            . implode(',', array_column($entry['inputs'], 'type'))
            . ')'
        );
    }

    public static function encodeParams(array $inputs, array $params): array
    {
        $encoded = array_map(
            fn ($param, $idx) => Type::encode($param, $inputs[$idx]['type']),
            $params,
            array_keys($params)
        );

        return $encoded;
    }

    public function decode($data): mixed
    {
        $outputTypes = array_column($this->entry['outputs'], 'type');

        return Type::decode($data, $outputTypes);
    }
}
