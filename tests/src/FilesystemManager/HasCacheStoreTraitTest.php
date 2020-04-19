<?php

namespace Nip\Filesystem\Tests\FilesystemManager;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Cached\CachedAdapter;
use Nip\Filesystem\FileDisk;
use Nip\Filesystem\FilesystemManager;
use Nip\Filesystem\Tests\AbstractTest;

/**
 * Class HasCacheStoreTraitTest
 * @package Nip\Filesystem\Tests\FilesystemManager
 */
class HasCacheStoreTraitTest extends AbstractTest
{
    public function test_create_no_cache()
    {
        $config = ['root' => TEST_FIXTURE_PATH];
        $manager = new FilesystemManager();
        $disk = $manager->createLocalDriver($config);
        self::assertInstanceOf(FileDisk::class, $disk);
        self::assertInstanceOf(Local::class, $disk->getAdapter());
    }

    public function test_create_cache_true()
    {
        $config = ['root' => TEST_FIXTURE_PATH, 'cache' => true];
        $manager = new FilesystemManager();
        $disk = $manager->createLocalDriver($config);
        self::assertInstanceOf(FileDisk::class, $disk);
        self::assertInstanceOf(CachedAdapter::class, $disk->getAdapter());
        self::assertFalse( $disk->getConfig()->has('cache'));
    }
}
