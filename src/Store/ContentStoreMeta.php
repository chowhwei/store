<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\ContentStoreMeta as ContentStoreMetaContract;
use Illuminate\Database\Eloquent\Model;

class ContentStoreMeta extends Model implements ContentStoreMetaContract
{
    protected $fillable = [
        'key', 'size', 'reference_count'
    ];

    /** @var callable $connectionSetter */
    protected $connectionSetter;

    public function setConnectionSetter($connectionSetter)
    {
        $this->connectionSetter = $connectionSetter;
    }

    public function saveMeta(string $key, int $size)
    {
        $model = $this->newQuery();
        if(is_callable($this->connectionSetter)){
            $model = call_user_func($this->connectionSetter, $model);
        }
        /**
         * 用key去检索，如果没有，用key和size/reference_count去构建
         */
        $model = $model->firstOrCreate([
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