<?php

namespace Nip\Filesystem;

use Psr\SimpleCache\CacheInterface;
use League\Flysystem\Cached\Storage\AbstractCache;

/**
 * Class Cache
 * @package Nip\Filesystem
 */
class Cache extends AbstractCache
{
    /**
     * The cache repository implementation.
     *
     * @var \Psr\SimpleCache\CacheInterface
     */
    protected $repository;

    /**
     * The cache key.
     *
     * @var string
     */
    protected $key;

    /**
     * The cache expiration time in seconds.
     *
     * @var int|null
     */
    protected $expire;

    /**
     * Create a new cache instance.
     *
     * @param \Psr\SimpleCache\CacheInterface $repository
     * @param string $key
     * @param int|null $expire
     * @return void
     */
    public function __construct(CacheInterface $repository, $key = 'flysystem', $expire = null)
    {
        $this->key = $key;
        $this->expire = $expire;
        $this->repository = $repository;
    }

    /**
     * Load the cache.
     *
     * @return void
     */
    public function load()
    {
        $contents = $this->repository->get($this->key);

        if (!is_null($contents)) {
            $this->setFromStorage($contents);
        }
    }

    /**
     * Persist the cache.
     *
     * @return void
     */
    public function save()
    {
        $contents = $this->getForStorage();

        $this->repository->set($this->key, $contents, $this->expire);
    }
}