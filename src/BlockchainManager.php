<?php

namespace Blokpax\Web3;

use Exception;

class BlockchainManager
{
    public function __construct(protected array $config = []) {}

    public function defaultDriver()
    {
        return $this->config['driver'] ?? null;
    }

    public function config(?string $driver): array
    {
        return $this->config['drivers'][$driver] ?: [];
    }

    public function driver(?string $driver = null): Provider
    {
        $driver = $driver ?: $this->defaultDriver();

        $config = $this->config($driver);

        if (empty($config['url'])) {
            throw new Exception("Driver '{$driver}' missing configuration 'url' for provider.");
        }

        return new Provider($config['url']);
    }

    public function eth()
    {
        return $this->driver('eth');
    }

    public function polygon()
    {
        return $this->driver('polygon');
    }
}
