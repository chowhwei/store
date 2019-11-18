<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\OssClient as OssClientContract;
use Chowhwei\Store\Contracts\StoreConfig;
use Exception;
use OSS\Core\OssException;
use OSS\OssClient as AliyunOssClient;

class OssClient implements OssClientContract
{
    /** @var AliyunOssClient $aliyunOssClient */
    protected $aliyunOssClient;
    /** @var string $bucket */
    protected $bucket;
    /** @var string $app */
    protected $app;

    /**
     * OssClient constructor.
     * @param StoreConfig $storeConfig
     * @throws OssException
     */
    public function __construct(StoreConfig $storeConfig)
    {
        $this->aliyunOssClient = new AliyunOssClient($storeConfig->getOssKeyId(), $storeConfig->getOssKeySecret(), $storeConfig->getOssEndPoint(), false);
        $this->bucket = $storeConfig->getOssBucket();
        $this->app = $storeConfig->getStoreApp();
    }

    public function get(string $id, $default = null)
    {
        $key = $this->getLimittedId($id);
        try{
            $object = $this->aliyunOssClient->getObject($this->bucket, $key);

            return unserialize($object);
        }catch(OSSException $ex){
            if($ex->getErrorCode() == 'NoSuchKey'){
                return $default;
            }
            throw $ex;
        }
    }

    public function set(string $id, $data): bool
    {
        $key = $this->getLimittedId($id);
        try{
            $this->aliyunOssClient->putObject($this->bucket, $key, serialize($data));
            return true;
        }catch(OSSException $ex){
            if($ex->getErrorCode() == 'NoSuchBucket'){
                throw new Exception('Invalid AliyunOss id, bucket not exists');
            }
            throw $ex;
        }
    }

    public function del(string $id): bool
    {
        $key = $this->getLimittedId($id);
        try{
            $this->aliyunOssClient->deleteObject($this->bucket, $key);
            return true;
        }catch(OSSException $ex){
            if($ex->getErrorCode() == 'NoSuchBucket'){
                throw new Exception('Invalid AliyunOss id, bucket not exists');
            }
            return false;
        }
    }

    protected function getLimittedId(string $id)
    {
        return $this->app . '/' . $id;
    }
}