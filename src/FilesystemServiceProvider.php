<?php

namespace Nip\Filesystem;

use Nip\Container\ServiceProviders\Providers\AbstractServiceProvider;
use Nip\Container\ServiceProviders\Providers\BootableServiceProviderInterface;

/**
 * Class FilesystemServiceProvider
 * @package Nip\Filesystem
 *
 * @inspiration https://github.com/laravel/framework/blob/5.4/src/Illuminate/Filesystem/FilesystemServiceProvider.php
 *
 */
class FilesystemServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return ['files', 'filesystem', 'filesystem.disk'];
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerNativeFilesystem();
        $this->registerFlysystem();
    }

    public function boot()
    {
        $this->mergeDefaultFilesystem();
    }

    /**
     * Register the native filesystem implementation.
     *
     * @return void
     */
    protected function registerNativeFilesystem()
    {
        $this->getContainer()->share('files', function () {
            return new Filesystem;
        });
    }

    /**
     * Register the driver based filesystem.
     *
     * @return void
     */
    protected function registerFlysystem()
    {
        $this->registerManager();

        $defaultDriver = $this->getDefaultDriver();
        if ($defaultDriver) {
            $this->getContainer()->share('filesystem.disk', function () {
                return app('filesystem')->disk($this->getDefaultDriver());
        });
}

        $cloudDriver = $this->getDefaultDriver();
        if ($cloudDriver) {
            $this->getContainer()->share('filesystem.cloud', function () {
                return app('filesystem')->disk($this->getCloudDriver());
            });
        }
    }

    /**
     * Register the filesystem manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->getContainer()->share('filesystem', function () {
            $app = $this->getContainer()->has('app') ? $this->getContainer()->get('app') : null;

            return new FilesystemManager($app);
        });
    }

    /**
     * Get the default file driver.
     *
     * @return string
     */
    protected function getDefaultDriver()
    {
        return function_exists('config') && function_exists('app') ? config('filesystems.default') : null;
    }

    /**
     * Get the default cloud based file driver.
     *
     * @return string
     */
    protected function getCloudDriver()
    {
        return function_exists('config') ? config('filesystems.cloud') : null;
    }

    protected function mergeDefaultFilesystem()
    {
        $config = app('config');

        if ($config->has('filesystems')) {
            return;
        }

        $urlUploads = defined('UPLOADS_URL') ? UPLOADS_URL : '';

        $config = new Config([
            'filesystems' => [
                'disks' => [
                    'local' => [
                        'driver' => 'local',
                        'root' => UPLOADS_PATH,
                    ],
                    'public' => [
                        'driver' => 'local',
                        'root' => UPLOADS_PATH,
                        'url' => $urlUploads,
                        'visibility' => 'public',
                    ]
                ]
            ]
        ]);
        app('config')->merge($config);
    }
}
