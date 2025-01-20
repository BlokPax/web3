<?php

use Blokpax\Web3\Abi;
use Blokpax\Web3\Contract;
use Blokpax\Web3\Contracts\EthContract;
use Blokpax\Web3\Provider;
use Blokpax\Web3\Transaction;
use Blokpax\Web3\Types\Address;
use Blokpax\Web3\Types\UInt256;

beforeEach(function () {
    $this->provider = new Provider('http://mock-rpc-url');
    $this->abiJson = json_encode([
        [
            'type'    => 'function',
            'name'    => 'balanceOf',
            'inputs'  => [['name' => 'account', 'type' => 'address']],
            'outputs' => [['name' => 'balance', 'type' => 'uint256']],
        ],
        [
            'type'    => 'function',
            'name'    => 'ownerOf',
            'inputs'  => [['name' => 'token', 'type' => 'uint256']],
            'outputs' => [['name' => 'owner', 'type' => 'address']],
        ],
    ]);
    $this->contract = new Contract($this->abiJson, '0x1234567890abcdef1234567890abcdef12345678');
});

it('Provider initializes with RPC URL', function () {
    expect($this->provider)->toBeInstanceOf(Provider::class);
});

it('ABI loads from JSON', function () {
    $abi = Abi::fromJson($this->abiJson);
    expect($abi)->toBeInstanceOf(Abi::class);
});

it('Contract initializes with ABI and Address', function () {
    expect($this->contract)->toBeInstanceOf(Contract::class);
});

it('throws an exception with an invalid address value', function () {
    $tx = $this->contract->balanceOf(new Address('0xabcdefabcdefabcdefabcdefabcdefabcdefabcde'));
})->throws(InvalidArgumentException::class);

it('encodes contract methods', function () {
    $tx = $this->contract->balanceOf(new Address('0x0000000000000000000000000000000000001234'));
    expect($tx)->toBeInstanceOf(Transaction::class);
    expect($tx->toArray())->toHaveKeys(['to', 'data']);
});

it('Ethereum Address validation', function () {
    expect(fn () => new Address('invalid_address'))->toThrow(InvalidArgumentException::class);
    expect(new Address('0x0000000000000000000000000000000000001234'))->toBeInstanceOf(Address::class);
});

it('UInt256 encoding works', function () {
    $uint = new UInt256(100);
    expect($uint)->toBeInstanceOf(UInt256::class);
    expect(UInt256::encode(100))->toBeString();
});

// Mocking Provider response test

class BalanceOfMockProvider extends Provider
{
    protected function sendRequest(string $payload): array
    {
        return ['jsonrpc' => '2.0', 'id' => 1, 'result' => '0x123456'];
    }
}

class OwnerOfMockProvider extends Provider
{
    protected function sendRequest(string $payload): array
    {
        return ['jsonrpc' => '2.0', 'id' => 1, 'result' => sprintf('%064s', '55555')];
    }
}

it('Decodes balanceOf result from mock provider', function () {
    $mockProvider = new BalanceOfMockProvider('http://mock-rpc-url');
    $tx = $this->contract->balanceOf(new Address('0x0000000000000000000000000000000000001234'));
    $result = $mockProvider->call($tx);
    expect($result)->toBe(1193046);
});

it('Decodes ownerOf result from mock provider', function () {
    $mockProvider = new OwnerOfMockProvider('http://mock-rpc-url');
    $tx = $this->contract->ownerOf(12345);
    $result = $mockProvider->call($tx);
    expect($result)->toBe(sprintf('0x%040s', '55555'));
});

it('initializes a generic erc721 contract', function () {
    $mockProvider = new BalanceOfMockProvider('http://mock-rpc-url');
    $contract = EthContract::erc721(sprintf('0x%040s', '12345'));
    $tx = $contract->balanceOf(sprintf('0x%040s', 12345));
    $result = $mockProvider->call($tx);
    expect($result)->toBe(1193046);
});

it('initializes a generic erc1155  contract', function () {
    $mockProvider = new BalanceOfMockProvider('http://mock-rpc-url');
    $contract = EthContract::erc1155(sprintf('0x%040s', '12345'));
    $tx = $contract->balanceOf(sprintf('0x%040s', 12345), 1);
    $result = $mockProvider->call($tx);
    expect($result)->toBe(1193046);
});

it('initializes a generic erc20 contract', function () {
    $mockProvider = new BalanceOfMockProvider('http://mock-rpc-url');
    $contract = EthContract::erc20(sprintf('0x%040s', '12345'));
    $tx = $contract->balanceOf(sprintf('0x%040s', 12345));
    $result = $mockProvider->call($tx);
    expect($result)->toBe(1193046);
});
