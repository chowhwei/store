<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\ConfigLoader as ConfigLoaderContract;
use Kohana;
use Kohana_Exception;
use Illuminate\Config\Repository as Config;

class KohanaConfigLoader implements ConfigLoaderContract
{
    /**
     * @param $file
     * @return Config
     * @throws Kohana_Exception
     */
    public function load($file)
    {
        return new Config((array)Kohana::$config->load($file));
    }
}