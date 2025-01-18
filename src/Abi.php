<?php

namespace Blokpax\Web3;

use InvalidArgumentException;
use kornrunner\Keccak;

class Abi
{
    protected array $methods = [];

    public function __construct(protected array $abi = [])
    {
        foreach ($abi as $entry) {
            if ($entry['type'] === 'function') {
                $this->methods[$entry['name']][] = $entry;
            }
        }
    }

    public static function fromJson(string $abi): static
    {
        $decoded = json_decode($abi, true);
        if ($decoded === null) {
            throw new InvalidArgumentException('ABI must be a valid JSON string.');
        }

        return new static($decoded);
    }

    public static function fromArray(array $abi): static
    {
        return new static($abi);
    }

    public function hasMethod(string $method): bool
    {
        return isset($this->methods[$method]);
    }

    public function getEntry(string $method, array $params): AbiEntry
    {
        if (! $this->hasMethod($method)) {
            throw new InvalidArgumentException("Method {$method} does not exist in ABI.");
        }

        $signature = $this->getMethodSignature($method, $params);

        return new AbiEntry($signature);
    }

    public function getMethodSignature(string $method, array $params): array
    {
        // Select the correct method signature based on params
        foreach ($this->methods[$method] ?? [] as $entry) {
            if (count($entry['inputs']) === count($params)) {
                return $entry;
            }
        }

        throw new InvalidArgumentException("No matching method signature found for {$method}.");
    }

    public function encodeFunctionCall(array $signature, array $params): string
    {
        // Encode function selector and parameters
        $selector = static::hash($signature['name'] . '(' . implode(',', array_column($signature['inputs'], 'type')) . ')');
        $encodedParams = array_map(fn ($param, $index) => Type::encode($param, $signature['inputs'][$index]['type']), $params, array_keys($params));

        return '0x' . substr($selector, 0, 8) . implode('', $encodedParams);
    }

    public static function hash($value)
    {
        return Keccak::hash($value, 256);
    }
}
