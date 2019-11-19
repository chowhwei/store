<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\ConfigLoader as ConfigLoaderContract;
use Illuminate\Config\Repository;

class ConfigLoader implements ConfigLoaderContract
{
    public function load($file)
    {
        return new Repository(config($file));
    }
}