<?php

namespace Nip\Filesystem\File;

use League\Flysystem\FilesystemInterface;
use Nip\Filesystem\FileDisk;

/**
 * Trait HasFilesystem
 * @package Nip\Filesystem\File
 */
trait HasFilesystem
{

    /**
     * @var FilesystemInterface|FileDisk
     */
    protected $filesystem;

    /**
     * Set the Filesystem object.
     *
     * @param FilesystemInterface $filesystem
     *
     * @return $this
     */
    public function setFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Retrieve the Filesystem object.
     *
     * @return FilesystemInterface|FileDisk
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

}