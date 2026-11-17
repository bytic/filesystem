<?php

namespace Nip\Filesystem\Tests;

use League\Flysystem\Local\LocalFilesystemAdapter;
use Nip\Config\Config;
use Nip\Container\Container;
use Nip\Filesystem\FileDisk;
use Nip\Filesystem\FilesystemManager;
use Nip\Filesystem\Utility\S3Helper;

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

    public function testS3UrlsIncludeNormalizedRoot()
    {
        if (!class_exists(\League\Flysystem\AwsS3V3\AwsS3V3Adapter::class)) {
            self::markTestSkipped('The S3 adapter is not installed.');
        }
        $manager = new FilesystemManager();
        $config = [
            'bucket' => 'example',
            'region' => 'us-east-1',
            'key' => 'key',
            'secret' => 'secret',
            'root' => 'folder',
        ];
        $disk = $manager->createS3Driver($config + ['url' => 'https://cdn.example.com']);
        self::assertSame('https://cdn.example.com/folder/file.txt', $disk->getUrl('/file.txt'));

        $disk = $manager->createS3Driver($config);
        self::assertSame('https://example.s3.amazonaws.com/folder/file.txt', $disk->getUrl('file.txt'));
    }

    public function testS3ConfigAllowsDefaultCredentialProvider()
    {
        $config = S3Helper::formatS3Config(['bucket' => 'example', 'region' => 'us-east-1']);
        self::assertArrayNotHasKey('credentials', $config);
    }

    public function testLocalDriverAcceptsEmptyDiskConfiguration()
    {
        $manager = new class extends FilesystemManager {
            public function makeDisk($adapter)
            {
                return $this->createDisk($adapter, []);
            }
        };
        $disk = $manager->makeDisk(new LocalFilesystemAdapter(TEST_FIXTURE_PATH . '/storage'));
        self::assertInstanceOf(FileDisk::class, $disk);
    }
}