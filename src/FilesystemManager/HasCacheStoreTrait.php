<?php

namespace Nip\Filesystem\FilesystemManager;

use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\Storage\Memory as MemoryStore;
use Nip\Filesystem\Cache;
use Nip\Utility\Arr;

/**
 * Trait HasCacheStoreTrait
 * @package Nip\Filesystem\FilesystemManager
 */
trait HasCacheStoreTrait
{
    /**
     * @param $adapter
     * @param $config
     */
    protected function checkForCacheNeeded(&$adapter, &$config)
    {
        $cache = Arr::pull($config, 'cache');
        if (!empty($cache)) {
            $adapter = $this->createCacheAdapter($adapter, $cache);
        }
    }

    /**
     * @param $adapter
     * @param $cache
     * @return CachedAdapter
     */
    protected function createCacheAdapter($adapter, $cache)
    {
        return new CachedAdapter($adapter, $this->createCacheStore($cache));
    }


    /**
     * Create a cache store instance.
     *
     * @param mixed $config
     * @return \League\Flysystem\Cached\CacheInterface|MemoryStore|Cache
     *
     * @throws \InvalidArgumentException
     */
    protected function createCacheStore($config)
    {
        if ($config === true) {
            return new MemoryStore;
        }

        $cacheManager = $this->getCacheManager();

        return new Cache(
            $cacheManager->store($config['store']),
            $config['prefix'] ?? 'flysystem',
            $config['expire'] ?? null
        );
    }

    /**
     * @return mixed
     */
    protected function getCacheManager()
    {
        return app('cache');
    }
}
