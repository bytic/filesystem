<?php
declare(strict_types=1);

namespace Nip\Filesystem\Utility;

class S3Helper
{
    /**
     * Format the given S3 configuration with the default options.
     *
     * @param array $config
     * @return array
     */
    public static function formatS3Config(array $config)
    {
        $config += ['version' => 'latest'];
        if ($config['key'] && $config['secret']) {
            $config['credentials'] = [
                'key' => $config['key'],
                'secret' => $config['secret'],
            ];
        }
        if (empty($config['endpoint'])) {
            unset($config['endpoint']);
        }

        return $config;
    }
}