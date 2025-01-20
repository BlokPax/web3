<?php

namespace Blokpax\Web3\Contracts;

use Blokpax\Web3\Contract;

abstract class EthContract
{
    const CONTRACT_ERC721 = 'ERC721';
    const CONTRACT_ERC1155 = 'ERC1155';
    const CONTRACT_ERC20 = 'ERC20';

    protected static $abis = [
        'ERC721'  => 'Erc721.json',
        'ERC1155' => 'Erc1155.json',
        'ERC20'   => 'Erc20.json',
    ];

    public static function erc721(string $address)
    {
        return new Contract(static::loadAbi(static::abiPath(
            static::$abis[static::CONTRACT_ERC721]
        )), $address);
    }

    public static function erc1155(string $address)
    {
        return new Contract(static::loadAbi(static::abiPath(
            static::$abis[static::CONTRACT_ERC1155]
        )), $address);
    }

    public static function erc20(string $address)
    {
        return new Contract(static::loadAbi(static::abiPath(
            static::$abis[static::CONTRACT_ERC20]
        )), $address);
    }

    public static function loadAbi($path): string
    {
        return file_get_contents($path);
    }

    public static function abiPath($file): string
    {
        return rtrim(__DIR__, '/') . '/../ABI/' . ltrim($file, '/');
    }
}
