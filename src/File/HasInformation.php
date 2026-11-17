<?php

namespace Nip\Filesystem\File;

trait HasInformation
{

    /**
     * Check whether the entree is a directory.
     *
     * @return bool
     */
    public function isDir()
    {
        return $this->getType() === 'dir';
    }

    /**
     * Check whether the entree is a file.
     *
     * @return bool
     */
    public function isFile()
    {
        return $this->getType() === 'file';
    }

    /**
     * Retrieve the entree type (file|dir).
     *
     * @return string file or dir
     */
    public function getType()
    {
        if ($this->filesystem->fileExists($this->path)) {
            return 'file';
        }
        return 'dir';
    }

    /**
     * Get the file's timestamp.
     *
     * @return string|false The timestamp or false on failure.
     */
    public function getTimestamp()
    {
        return $this->filesystem->lastModified($this->path);
    }

    /**
     * Get the file's mimetype.
     *
     * @return string|false The file mime-type or false on failure.
     */
    public function getMimetype()
    {
        return $this->filesystem->mimeType($this->path);
    }

    /**
     * Get the file's visibility.
     *
     * @return string|false The visibility (public|private) or false on failure.
     */
    public function getVisibility()
    {
        return $this->filesystem->visibility($this->path);
    }

    /**
     * Get the file's metadata.
     *
     * @return array|false The file metadata or false on failure.
     */
    public function getMetadata()
    {
        if ($this->filesystem->fileExists($this->path)) {
            return [
                'type' => 'file',
                'path' => $this->path,
                'timestamp' => $this->filesystem->lastModified($this->path),
                'size' => $this->filesystem->fileSize($this->path),
            ];
        }
        if ($this->filesystem->directoryExists($this->path)) {
            return ['type' => 'dir', 'path' => $this->path];
        }
        return false;
    }

    /**
     * Get the file size.
     *
     * @return int|false The file size or false on failure.
     */
    public function getSize()
    {
        return $this->filesystem->fileSize($this->path);
    }

}
