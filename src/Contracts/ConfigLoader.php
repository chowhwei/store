<?php

namespace Chowhwei\Store\Contracts;

use Illuminate\Config\Repository as Config;

interface ConfigLoader
{
    /**
     * @param string $file
     * @return Config
     */
    public function load($file);
}