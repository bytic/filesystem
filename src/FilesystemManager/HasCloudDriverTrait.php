<?php

namespace Nip\Filesystem\FilesystemManager;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter as S3Adapter;
use League\Flysystem\FilesystemOperator;
use Nip\Filesystem\FileDisk;
use Nip\Filesystem\Utility\S3Helper;

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
     * @return FilesystemOperator|FileDisk
     */
    public function createS3Driver(array $config)
    {
        $s3Config = $this->formatS3Config($config);
        $root = $s3Config['root'] ?? '';
        $options = $config['options'] ?? [];

        $client = new S3Client($s3Config);
        $config['client'] = $client;
        $disk = $this->createDisk(
            new S3Adapter($client, $s3Config['bucket'], $root, null, null, $options),
            $config
        );
        return $this->adapt($disk);
    }

    /**
     * Format the given S3 configuration with the default options.
     *
     * @param array $config
     * @return array
     */
    protected function formatS3Config(array $config)
    {
        return S3Helper::formatS3Config($config);
    }
}
