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
        return $this->filesystem->has($this->path);
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
        return $this->filesystem->write($this->path, $content);
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
        return $this->filesystem->writeStream($this->path, $resource);
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
        return $this->filesystem->update($this->path, $content);
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
        return $this->filesystem->updateStream($this->path, $resource);
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
        return $this->filesystem->put($this->path, $content);
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
        return $this->filesystem->putStream($this->path, $resource);
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
        if ($this->filesystem->rename($this->path, $newpath)) {
            $this->path = $newpath;

            return true;
        }

        return false;
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
        if ($this->filesystem->copy($this->path, $newpath)) {
            return new self($this->filesystem, $newpath);
        }

        return false;
    }

    /**
     * Delete the file.
     *
     * @return bool success boolean
     */
    public function delete()
    {
        return $this->filesystem->delete($this->path);
    }
}