<?php

namespace Nip\Filesystem\File;

/**
 * Trait HasOperations
 * @package Nip\Filesystem\File
 */
trait HasOperations
{

    /**
     * Check whether the file exists.
     *
     * @return bool
     */
    public function exists()
    {
        return $this->filesystem->fileExists($this->path) || $this->filesystem->directoryExists($this->path);
    }

    /**
     * Read the file.
     *
     * @return string|false file contents
     */
    public function read()
    {
        return $this->filesystem->read($this->path);
    }

    /**
     * Read the file as a stream.
     *
     * @return resource|false file stream
     */
    public function readStream()
    {
        return $this->filesystem->readStream($this->path);
    }

    /**
     * Write the new file.
     *
     * @param string $content
     *
     * @return bool success boolean
     */
    public function write($content)
    {
        $this->filesystem->write($this->path, $content);
        return true;
    }

    /**
     * Write the new file using a stream.
     *
     * @param resource $resource
     *
     * @return bool success boolean
     */
    public function writeStream($resource)
    {
        $this->filesystem->writeStream($this->path, $resource);
        return true;
    }

    /**
     * Update the file contents.
     *
     * @param string $content
     *
     * @return bool success boolean
     */
    public function update($content)
    {
        if (!$this->filesystem->fileExists($this->path)) {
            return false;
        }
        return $this->write($content);
    }

    /**
     * Update the file contents with a stream.
     *
     * @param resource $resource
     *
     * @return bool success boolean
     */
    public function updateStream($resource)
    {
        if (!$this->filesystem->fileExists($this->path)) {
            return false;
        }
        return $this->writeStream($resource);
    }

    /**
     * Create the file or update if exists.
     *
     * @param string $content
     *
     * @return bool success boolean
     */
    public function put($content)
    {
        if (is_resource($content)) {
            return $this->writeStream($content);
        }
        return $this->write($content);
    }

    /**
     * Create the file or update if exists using a stream.
     *
     * @param resource $resource
     *
     * @return bool success boolean
     */
    public function putStream($resource)
    {
        return $this->writeStream($resource);
    }

    /**
     * @param null|string $name
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($name = null): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return $this->filesystem->download($this->getPath(), $name ? $name : $this->getName());
    }

    /**
     * Rename the file.
     *
     * @param string $newpath
     *
     * @return bool success boolean
     */
    public function rename($newpath)
    {
        $this->filesystem->move($this->path, $newpath);
        $this->setPath($newpath);
        return true;
    }

    /**
     * Copy the file.
     *
     * @param string $newpath
     *
     * @return self|false new file or false
     */
    public function copy($newpath)
    {
        $this->filesystem->copy($this->path, $newpath);
        return new self($this->filesystem, $newpath);
    }

    /**
     * Delete the file.
     *
     * @return bool success boolean
     */
    public function delete()
    {
        $this->filesystem->delete($this->path);
        return true;
    }
}