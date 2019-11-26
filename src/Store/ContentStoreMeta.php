<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\ContentStoreMeta as ContentStoreMetaContract;
use Illuminate\Database\Eloquent\Model;

class ContentStoreMeta extends Model implements ContentStoreMetaContract
{
    protected $fillable = [
        'key', 'size', 'reference_count'
    ];

    public function saveMeta(string $key, int $size)
    {
        /**
         * 用key去检索，如果没有，用key和size/reference_count去构建
         */
        $model = $this->newQuery()->firstOrCreate([
            'key' => $key
        ], [
            'size' => $size,
            'reference_count' => 1
        ]);
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