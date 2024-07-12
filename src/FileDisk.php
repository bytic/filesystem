<?php

namespace Nip\Filesystem;

use League\Flysystem\Config;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\AwsS3v3\AwsS3V3Adapter;
use League\Flysystem\Filesystem as Flysystem;
use League\Flysystem\PathNormalizer;
use Nip\Utility\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FlysystemAdapter
 * @package Nip\Filesystem
 */
class FileDisk extends Flysystem
{
    protected $adapter;

    protected $config;

    public function __construct(
        FilesystemAdapter $adapter,
        array $config = [],
        PathNormalizer $pathNormalizer = null
    ) {
        $this->adapter = $adapter;
        parent::__construct($adapter, $config, $pathNormalizer);

        $this->config = new Config($config);
    }

    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Store the uploaded file on the disk with a given name.
     *
     * @param string $path
     * @param UploadedFile $file
     * @param string $name
     * @param array $options
     * @return string|false
     */
    public function putFileAs($path, $file, $name, $options = [])
    {
        $stream = fopen($file->getRealPath(), 'r+');

        // Next, we will format the path of the file and store the file using a stream since
        // they provide better performance than alternatives. Once we write the file this
        // stream will get closed automatically by us so the developer doesn't have to.
        $this->write(
            $path = trim($path . '/' . $name, '/'),
            $stream,
            $options
        );

        if (is_resource($stream)) {
            fclose($stream);
        }
        return $this->fileExists($path) ? $path : false;
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param string $path
     * @return string
     */
    public function getUrl($path)
    {
        $path = str_replace('\\','/', $path);
        $path = str_replace('//','/', $path);

        $adapter = $this->getAdapter();

        if (method_exists($adapter, 'getUrl')) {
            return $adapter->getUrl($path);
        } elseif ($adapter instanceof AwsS3V3Adapter) {
            $path = ltrim($path,'/');
            return $this->getAwsUrl($adapter, $path);
        } elseif ($adapter instanceof LocalAdapter) {
            return $this->getLocalUrl($path);
        } else {
            throw new RuntimeException('This driver does not support retrieving URLs.');
        }
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param string $path
     * @return string
     */
    protected function getLocalUrl($path)
    {
        $config = $this->getConfig();

        // If an explicit base URL has been set on the disk configuration then we will use
        // it as the base URL instead of the default path. This allows the developer to
        // have full control over the base path for this filesystem's generated URLs.
        $configUrl = $config->get('url');
        if ($configUrl) {
            return $this->concatPathToUrl($configUrl, $path);
        }
        $path = '/storage/' . $path;
        // If the path contains "storage/public", it probably means the developer is using
        // the default disk to generate the path instead of the "public" disk like they
        // are really supposed to use. We will remove the public from this path here.
        if (Str::contains($path, '/storage/public/')) {
            return Str::replaceFirst('/public/', '/', $path);
        } else {
            return $path;
        }
    }

    /**
     * Get the URL for the file at the given path.
     *
     * @param \League\Flysystem\AwsS3v3\AwsS3Adapter $adapter
     * @param string $path
     * @return string
     */
    protected function getAwsUrl($adapter, $path)
    {
        // If an explicit base URL has been set on the disk configuration then we will use
        // it as the base URL instead of the default path. This allows the developer to
        // have full control over the base path for this filesystem's generated URLs.
        if (!is_null($url = $this->getConfig()->get('url'))) {
            return $this->concatPathToUrl($url, $adapter->getPathPrefix() . $path);
        }

        return $adapter->getClient()->getObjectUrl(
            $adapter->getBucket(),
            $adapter->getPathPrefix() . $path
        );
    }

    /**
     * Concatenate a path to a URL.
     *
     * @param string $url
     * @param string $path
     * @return string
     */
    protected function concatPathToUrl($url, $path)
    {
        return rtrim($url, '/') . '/' . ltrim($path, '/');
    }



    /**
     * Create a streamed response for a given file.
     *
     * @param  string  $path
     * @param  string|null  $name
     * @param  array|null  $headers
     * @param  string|null  $disposition
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function response($path, $name = null, array $headers = [], $disposition = 'inline')
    {
        $response = new \Symfony\Component\HttpFoundation\StreamedResponse;

        $filename = $name ?? basename($path);

        $disposition = $response->headers->makeDisposition(
            $disposition, $filename, $this->fallbackName($filename)
        );

        $response->headers->replace($headers + [
                'Content-Type' => $this->mimeType($path),
                'Content-Length' => $this->fileSize($path),
                'Content-Disposition' => $disposition,
            ]);

        $response->setCallback(function () use ($path) {
            $stream = $this->readStream($path);
            fpassthru($stream);
            fclose($stream);
        });

        return $response;
    }

    /**
     * Create a streamed download response for a given file.
     *
     * @param  string  $path
     * @param  string|null  $name
     * @param  array|null  $headers
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($path, $name = null, array $headers = [])
    {
        return $this->response($path, $name, $headers, 'attachment');
    }

    /**
     * Convert the string to ASCII characters that are equivalent to the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function fallbackName($name)
    {
        return str_replace('%', '', Str::ascii($name));
    }

    /**
     * @deprecated use mimeType
     */
    public function getMimetype(string $path): string
    {
        return $this->mimeType($path);
    }

    /**
     * @deprecated use fileSize
     */
    public function getSize(string $path): int
    {
        return $this->fileSize($path);
    }

    /**
     * @deprecated use deleteDirectory
     */
    public function deleteDir(string $location): void
    {
        $this->deleteDirectory($location);
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }
}
