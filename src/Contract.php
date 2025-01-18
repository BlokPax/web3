<?php

namespace Blokpax\Web3;

use InvalidArgumentException;

class Contract
{
    public Abi $abi;
    protected ?string $address;

    public function __construct(Abi|string|array $abi, ?string $address = null)
    {
        $this->address = $address ?? static::zeroAddress();

        if (is_string($abi)) {
            $abi = Abi::fromJson($abi);
        }

        if (is_array($abi)) {
            $abi = Abi::fromArray($abi);
        }

        $this->abi = $abi;
    }

    public function __call($method, $params)
    {
        if (! $this->abi->hasMethod($method)) {
            throw new InvalidArgumentException("Method {$method} does not exist in ABI.");
        }

        // $signature = $this->abi->getMethodSignature($method, $params);
        // $encodedCall = $this->abi->encodeFunctionCall($signature, $params);
        $abiEntry = $this->abi->getEntry($method, $params);

        return new Transaction($this->address, $abiEntry, $params);
    }

    public static function zeroAddress(): string
    {
        return sprintf('0x%040s', '0');
    }
}
