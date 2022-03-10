<?php

namespace Nip\Filesystem\Tests;

use League\Flysystem\Local\LocalFilesystemAdapter;
use Nip\Config\Config;
use Nip\Container\Container;
use Nip\Filesystem\FileDisk;
use Nip\Filesystem\FilesystemManager;

/**
 * Class FilesystemManagerTest
 * @package Nip\Filesystem\Tests
 */
class FilesystemManagerTest extends AbstractTest
{

    public function test_create_by_reference()
    {
        $configArray = require TEST_FIXTURE_PATH . '/config/config.php';
        $config = new Config(['filesystems' => $configArray]);
        Container::getInstance()->set('config', $config);

        $manager = new FilesystemManager();
        $diskLocal = $manager->disk('local');
        self::assertInstanceOf(FileDisk::class, $diskLocal);
        self::assertInstanceOf(LocalFilesystemAdapter::class, $diskLocal->getAdapter());

        $diskPublic = $manager->disk('public');
        self::assertInstanceOf(FileDisk::class, $diskLocal);
        self::assertInstanceOf(LocalFilesystemAdapter::class, $diskLocal->getAdapter());

        self::assertSame($diskLocal, $diskPublic);
    }
}