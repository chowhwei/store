<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\ConfigLoader as ConfigLoaderContract;
use Config;
use Config_File;
use Illuminate\Config\Repository;
use Kohana_Exception;

/**
 * 在kohana下使用的
 * Class KohanaConfigLoader
 * @package Chowhwei\Store\Store
 */
class KohanaConfigLoader implements ConfigLoaderContract
{
    /** @var Config $config */
    protected $config;

    public function __construct(Config $config, Config_File $configFile)
    {
        $this->config = $config;
        $this->config->attach($configFile);
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