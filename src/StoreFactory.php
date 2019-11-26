<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\ContentStore as ContentStoreContract;
use Chowhwei\Store\Contracts\StoreClient;
use Chowhwei\Store\Contracts\StoreFactory as StoreFactoryContract;
use Chowhwei\Store\Store\ContentStoreMeta;
use Chowhwei\Store\Store\FileClient;
use Chowhwei\Store\Store\OssClient;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Model;

class StoreFactory implements StoreFactoryContract
{
    /** @var ContentStore[] $apps */
    protected $apps = [];

    protected $clients = [];
    protected $metas = [];

    /** @var Repository $config */
    protected $config;

    public function __construct()
    {
    }

    /**
     * @param string $app
     * @return ContentStoreContract
     * @throws \OSS\Core\OssException
     */
    public function app(string $app = 'default')
    {
        return $this->getApp($app);
    }

    /**
     * @param string $app
     * @return ContentStoreContract
     * @throws \OSS\Core\OssException
     * @throws \Exception
     */
    protected function getApp($app)
    {
        if (class_exists('Kohana')) {
            $this->config = new Repository(\Kohana::$config->load('store')->as_array());
        } else {
            $this->config = new Repository(config('store'));
        }
        if ($app == 'default') {
            $app = $this->config->get('default');
        }

        if (!isset($this->apps[$app])) {
            $config = $this->config->get("store.{$app}");

            $ossClient = $this->getClient($config['oss'], $app);
            $fileClient = $this->getClient($config['file'], $app);
            $meta = $this->getMeta($config['meta']);

            $cs = new ContentStore($ossClient, $fileClient);
            if (!is_null($meta)) {
                $cs->setMeta($meta);
            }
            $this->apps[$app] = $cs;
        }

        return $this->apps[$app];
    }

    /**
     * @param string $name
     * @return ContentStoreMeta|null
     * @throws \Exception
     */
    protected function getMeta($name)
    {
        if ($name === false) {
            return null;
        }
        if (!isset($this->metas[$name])) {
            $this->metas[$name] = $this->makeMeta($this->config->get("meta.{$name}"));
        }
        return $this->metas[$name];
    }

    /**
     * @param array $config
     * @return ContentStoreMeta
     * @throws \Exception
     */
    protected function makeMeta($config)
    {
        $meta = new ContentStoreMeta();
        $meta->setConnectionSetter(function (Model $model) use ($config) {
            return $model->setTable($config['table'])->setConnection($config['connection']);
        });
        return $meta;
    }

    /**
     * @param string $name
     * @param string $dir
     * @return mixed
     * @throws \OSS\Core\OssException
     */
    protected function getClient($name, $dir)
    {
        if (!isset($this->clients[$name])) {
            $this->clients[$name] = $this->makeClient($this->config->get("client.{$name}"), $dir);
        }

        return $this->clients[$name];
    }

    /**
     * @param array $config
     * @param string $dir
     * @return StoreClient
     * @throws \OSS\Core\OssException
     * @throws \Exception
     */
    protected function makeClient($config, $dir)
    {
        switch (trim(strtolower($config['type']))) {
            case 'oss':
                return new OssClient($config, $dir);
                break;
            case 'file':
                return new FileClient($config, $dir);
                break;
            default:
                throw new \Exception(strtr('Invalid type :type', [
                    ':type' => $config['type']
                ]));
                break;
        }
    }
}