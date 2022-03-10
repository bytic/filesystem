<?php

namespace Nip\Filesystem;

use InvalidArgumentException;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemOperator;
use Nip\Config\Config;

/**
 * Class FilesystemManager
 * @package Nip\Filesystem
 */
class FilesystemManager
{
    use FilesystemManager\HasCloudDriverTrait;
    use FilesystemManager\HasDisksTrait;

    /**
     * The application instance.
     *
     * @var \Nip\Application
     */
    protected $app;

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];


    /**
     * Create a new filesystem manager instance.
     *
     * @param  \Nip\Application $app
     */
    public function __construct($app = null)
    {
        if ($app) {
            $this->app = $app;
        }
    }

    /**
     * Resolve the given disk.
     *
     * @param  string $name
     * @return FileDisk
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);
        if (empty($config)) {
            throw new InvalidArgumentException("No configuration found for Disk [{$name}].");
        }

        if (is_string($config)) {
            return $this->get($config);
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }
        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';
        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        } else {
            throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
        }
    }

    /**
     * Get the filesystem connection configuration.
     *
     * @param  string $name
     * @return array
     */
    protected function getConfig($name)
    {
        if (!function_exists('config')) {
            return null;
        }
        $config = config();
        $configName = "filesystems.disks.{$name}";
        if (!$config->has($configName)) {
            return null;
        }

        $value = $config->get($configName);

        return $value instanceof Config ? $value->toArray() : $value;
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array $config
     * @return FileDisk
     */
    protected function callCustomCreator(array $config)
    {
        $driver = $this->customCreators[$config['driver']]($this->app, $config);
        if ($driver instanceof FilesystemOperator) {
            return $this->adapt($driver);
        }

        return $driver;
    }

    /**
     * Adapt the filesystem implementation.
     *
     * @param  \League\Flysystem\FilesystemOperator $filesystem
     * @return \League\Flysystem\FilesystemOperator|FileDisk
     */
    protected function adapt(FilesystemOperator $filesystem)
    {
        return $filesystem;
//        return new FlysystemAdapter($filesystem);
    }

    /**
     * Create an instance of the local driver.
     *
     * @param  array $config
     * @return \League\Flysystem\FilesystemOperator
     */
    public function createLocalDriver($config)
    {
        $permissions = $config['permissions'] ?? [];
        $links = [];
//        $links = Arr::get($config, 'links') === 'skip'
//            ? LocalAdapter::SKIP_LINKS
//            : LocalAdapter::DISALLOW_LINKS;

        return $this->adapt(
            $this->createDisk(
                new LocalAdapter(
                    $config['root'],
                    null,
                    LOCK_EX
//                    $links,
//                    $permissions
                ),
                $config
            )
        );
    }

    /**
     * Create a Flysystem instance with the given adapter.
     *
     * @param  \League\Flysystem\FilesystemAdapter $adapter
     * @param  array $config
     * @return FileDisk
     */
    protected function createDisk(FilesystemAdapter $adapter, $config)
    {
//        $config = Arr::only($config, ['visibility', 'disable_asserts', 'url']);
//        $this->checkForCacheNeeded($adapter, $config);

        return new FileDisk($adapter, count($config) > 0 ? $config : null);
    }


    /**
     * Set the given disk instance.
     *
     * @param  string $name
     * @param  FileDisk $disk
     * @return void
     */
    public function set($name, $disk)
    {
        $this->disks[$name] = $disk;
    }

    /**
     * Get the default cloud driver name.
     *
     * @return string
     */
    public function getDefaultCloudDriver()
    {
        return config('filesystems.cloud');
    }
}
