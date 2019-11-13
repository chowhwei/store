<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\StoreClient;
use OSS\Core\OssException;
use OSS\OssClient as AliyunOssClient;
use Exception;

class OssClient implements StoreClient
{
    /** @var AliyunOssClient $aliyunOssClient */
    protected $aliyunOssClient;
    /** @var string $bucket */
    protected $bucket;
    /** @var string $app */
    protected $app;

    /**
     * OssClient constructor.
     * @param string $accessKeyId
     * @param string $accessKeySecret
     * @param string $endpoint
     * @param string $bucket
     * @param string $app
     * @throws OssException
     */
    public function __construct(string $accessKeyId, string $accessKeySecret, string $endpoint, string $bucket, string $app)
    {
        $this->aliyunOssClient = new AliyunOssClient($accessKeyId, $accessKeySecret, $endpoint, false);
        $this->bucket = $bucket;
        $this->app = $app;
    }

    public function get(string $id, $default = null): string
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