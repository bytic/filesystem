<?php

namespace Nip\Filesystem\FilesystemManager;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter as S3Adapter;
use League\Flysystem\FilesystemInterface;
use Nip\Filesystem\FileDisk;

/**
 * Trait HasCloudDriverTrait
 * @package Nip\Filesystem\FilesystemManager
 */
trait HasCloudDriverTrait
{
    /**
     * Create an instance of the Amazon S3 driver.
     *
     * @param array $config
     * @return FilesystemInterface|FileDisk
     */
    public function createS3Driver(array $config)
    {
        $s3Config = $this->formatS3Config($config);
        $root = $s3Config['root'] ?? null;
        $options = $config['options'] ?? [];

        return $this->adapt($this->createDisk(
            new S3Adapter(new S3Client($s3Config), $s3Config['bucket'], $root, $options),
            $config
        ));
    }

    /**
     * Format the given S3 configuration with the default options.
     *
     * @param array $config
     * @return array
     */
    protected function formatS3Config(array $config)
    {
        $config += ['version' => 'latest'];
        if ($config['key'] && $config['secret']) {
            $config['credentials'] = [
                'key' => $config['key'],
                'secret' => $config['secret'],
            ];
        }

        return $config;
    }
}
