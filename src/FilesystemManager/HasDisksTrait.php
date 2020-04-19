<?php

namespace Nip\Filesystem\FilesystemManager;

use Nip\Filesystem\FileDisk;

/**
 * Trait HasDisksTrait
 * @package Nip\Filesystem\FilesystemManager
 */
trait HasDisksTrait
{
    /**
     * The array of resolved filesystem drivers.
     *
     * @var FileDisk[]
     */
    protected $disks = [];

    /**
     * Get a filesystem instance.
     *
     * @param  string $name
     * @return FileDisk
     */
    public function disk($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->disks[$name] = $this->get($name);
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return config('filesystems.default');
    }

    /**
     * Attempt to get the disk from the local cache.
     *
     * @param  string $name
     * @return FileDisk
     */
    protected function get($name)
    {
        return isset($this->disks[$name]) ? $this->disks[$name] : $this->resolve($name);
    }
}
