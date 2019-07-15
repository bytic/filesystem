<?php

namespace Nip\Filesystem\Tests;

use Nip\Filesystem\Filesystem;
use Nip\Filesystem\FilesystemManager;
use Nip\Filesystem\FilesystemServiceProvider;

/**
 * Class FilesystemServiceProviderTest
 * @package Nip\Filesystem\Tests
 */
class FilesystemServiceProviderTest extends AbstractTest
{
    public function testRegister()
    {
        $provider = new FilesystemServiceProvider();
        $provider->register();

        $filesystem = $provider->getContainer()->get('files');
        self::assertInstanceOf(Filesystem::class, $filesystem);

        $filesystemManager = $provider->getContainer()->get('filesystem');
        self::assertInstanceOf(FilesystemManager::class, $filesystemManager);
    }
}
