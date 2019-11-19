<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\ConfigLoader as ConfigLoaderContract;
use Illuminate\Config\Repository;

class ConfigLoader implements ConfigLoaderContract
{
    /** @var \Illuminate\Contracts\Config\Repository $repository */
    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function load($file)
    {
        return new Repository($this->repository->get($file));
    }
}