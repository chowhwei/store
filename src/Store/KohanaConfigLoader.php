<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\ConfigLoader as ConfigLoaderContract;
use Config;
use Illuminate\Config\Repository;
use Kohana_Exception;

class KohanaConfigLoader implements ConfigLoaderContract
{
    /** @var Config $config */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $file
     * @return Repository
     * @throws Kohana_Exception
     */
    public function load($file)
    {
        return new Repository($this->config->load($file)->as_array());
    }
}