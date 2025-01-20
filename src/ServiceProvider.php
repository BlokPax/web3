<?php

namespace Blokpax\Web3;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function register()
    {
        $path = $this->configPath();
        $this->mergeConfigFrom($path, 'web3');

        $this->app->bind(BlockhainManager::class, function ($app) {
            return new BlockchainManager(config('web3'));
        });
    }

    private function configPath()
    {
        return __DIR__ . '/../config/web3.php';
    }

    public function boot()
    {
        $this->publishes([
            $this->configPath() => config_path('web3.php'),
        ], 'config');
    }
}
