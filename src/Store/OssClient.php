<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\StoreClient;
use Exception;
use OSS\Core\OssException;
use OSS\OssClient as AliyunOssClient;

class OssClient implements StoreClient
{
    /** @var AliyunOssClient $aliyunOssClient */
    protected $aliyunOssClient;
    /** @var string $bucket */
    protected $bucket;
    /** @var string $dir */
    protected $dir;

    /**
     * OssClient constructor.
     * @param array $config
     * @param string $dir
     * @throws OssException
     */
    public function __construct(array $config, string $dir)
    {
        $this->aliyunOssClient = new AliyunOssClient($config['oss_keyid'], $config['oss_keysecret'], $config['oss_endpoint'], false);
        $this->bucket = $config['oss_bucket'];
        $this->dir = $dir;
    }

    public function get(string $id, $default = null)
    {
        $key = $this->getLimittedId($id);
        try {
            $object = $this->aliyunOssClient->getObject($this->bucket, $key);

            return unserialize($object);
        } catch (OSSException $ex) {
            if ($ex->getErrorCode() == 'NoSuchKey') {
                return $default;
            }
            throw $ex;
        }
    }

    public function set(string $id, $data): bool
    {
        $key = $this->getLimittedId($id);
        try {
            $this->aliyunOssClient->putObject($this->bucket, $key, serialize($data));
            return true;
        } catch (OSSException $ex) {
            if ($ex->getErrorCode() == 'NoSuchBucket') {
                throw new Exception('Invalid AliyunOss id, bucket not exists');
            }
            throw $ex;
        }
    }

    public function del(string $id): bool
    {
        $key = $this->getLimittedId($id);
        try {
            $this->aliyunOssClient->deleteObject($this->bucket, $key);
            return true;
        } catch (OSSException $ex) {
            if ($ex->getErrorCode() == 'NoSuchBucket') {
                throw new Exception('Invalid AliyunOss id, bucket not exists');
            }
            return false;
        }
    }

    public function has(string $id): bool
    {
        $key = $this->getLimittedId($id);
        try {
            return $this->aliyunOssClient->doesObjectExist($this->bucket, $key);
        } catch (OSSException $ex) {
            if ($ex->getErrorCode() == 'NoSuchKey') {
                return false;
            }
            throw $ex;
        }
    }

    protected function getLimittedId(string $id)
    {
        return $this->dir . '/' . $id;
    }
}