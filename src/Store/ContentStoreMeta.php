<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\ContentStoreMeta as ContentStoreMetaContract;
use Illuminate\Database\Eloquent\Model;

class ContentStoreMeta extends Model implements ContentStoreMetaContract
{
    public function saveMeta(string $key, int $size)
    {
        $model = $this->newQuery()
            ->where('key', '=', $key)
            ->first();
        if(is_null($model))
        {
            $model = clone($this);
            $model->key = $key;
        }
        $model->size = $size;
        $model->save();
    }

    public function incrReference(string $key)
    {
        $this->newQuery()
            ->where('key', '=', $key)
            ->increment('reference_count');
    }

    public function decrReference(string $key)
    {
        $this->newQuery()
            ->where('key', '=', $key)
            ->where('reference_count', '>', 0)
            ->decrement('reference_count');
    }
}