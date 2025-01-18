<?php

namespace Blokpax\Web3;

use RuntimeException;

class Provider
{
    protected string $rpcUrl;

    public function __construct(string $rpcUrl)
    {
        $this->rpcUrl = $rpcUrl;
    }

    public function call(Transaction $tx): mixed
    {
        // Serialize the transaction and send it as a JSON-RPC request
        $payload = json_encode([
            'jsonrpc' => '2.0',
            'method'  => 'eth_call',
            'params'  => [$tx->toArray(), 'latest'],
            'id'      => 1,
        ]);

        $response = $this->sendRequest($payload);

        return $tx->decode($response['result']) ?? null;
    }

    protected function sendRequest(string $payload): array
    {
        $ch = curl_init($this->rpcUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $result = curl_exec($ch);
        if ($result === false) {
            throw new RuntimeException('RPC request failed: ' . curl_error($ch));
        }
        curl_close($ch);

        return json_decode($result, true);
    }
}
