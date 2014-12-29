<?php

namespace Kohana\Modules\Cache;

interface CacheInterface
{
    /**
     * Store data in cache.
     * 
     * @param string $key Key to store under
     * @param mixed $data Data to save
     * @return bool Success
     */
    public function store($key, $data);
    
    /**
     * Retrieves data which has been cached.
     * 
     * @param string $key The data to retrieve
     * @return mixed Data or null on failure
     */
    public function retrieve($key);
}
