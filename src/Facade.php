<?php

namespace Blokpax\Web3;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Facade extends BaseFacade
{
    protected static function getFacadeAccessor()
    {
        return 'web3';
    }
}
