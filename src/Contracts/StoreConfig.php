<?php

namespace Chowhwei\Store\Contracts;

interface StoreConfig
{
    public function getOssEndPoint(): string;

    public function getOssKeyId(): string;

    public function getOssKeySecret(): string;

    public function getOssBucket(): string;

    public function getNfsRoot(): string;

    public function getStoreApp(): string;
}