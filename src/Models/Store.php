<?php

namespace Chowhwei\Store\Models;

use Chowhwei\Store\Contracts\TableStore;
use Illuminate\Database\Eloquent\Model;

class Store extends Model implements TableStore
{
    const TABLE_NAME = ':bucket_store';

    protected $primaryKey = 'id';

    protected $fillable = ['hash_key', 'length', 'expires_at'];

    protected $hidden = ['id'];

    protected function setBucket(string $bucket)
    {
        return $this->setTable(strtr(self::TABLE_NAME, [
            ':bucket' => $bucket
        ]));
    }

    public function store(string $bucket, string $hash_key, int $length, string $expires_at = null)
    {
        if(is_null($this->get($bucket, $hash_key))){

        }
    }

    public function get(string $bucket, string $hash_key)
    {
        $this->setBucket($bucket)
            ->query()
            ->where('hash_key', '=', $hash_key)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>=', date('Y-m-d H:i:s'))
            ->select()
            ->first();
    }

    public function mark(string $bucket, string $hash_key)
    {
        // TODO: Implement mark() method.
    }

    public function unmark(string $bucket, string $hash_key)
    {
        // TODO: Implement unmark() method.
    }

    public function getExpires(string $bucket, int $page, int $page_size)
    {
        // TODO: Implement getExpires() method.
    }
}