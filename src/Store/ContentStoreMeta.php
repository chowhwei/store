<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\ContentStoreMeta as ContentStoreMetaContract;
use Illuminate\Database\Eloquent\Model;

class ContentStoreMeta extends Model implements ContentStoreMetaContract
{
    public function saveMeta(string $key, int $size)
    {
        $res = $this->newQuery()
            ->where('key', '=', $key)
            ->limit(1)
            ->get();
        if ($res->count() == 0) {
            $model = $this;
            $model->key = $key;
        } else {
            $model = $res[0];
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