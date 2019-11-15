<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\ConfigLoader as ConfigLoaderContract;

class ConfigLoader implements ConfigLoaderContract
{
    public function load($file)
    {
        return config($file);
    }
}