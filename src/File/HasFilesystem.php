<?php

namespace Nip\Filesystem\File;

use League\Flysystem\FilesystemOperator;
use Nip\Filesystem\FileDisk;

/**
 * Trait HasFilesystem
 * @package Nip\Filesystem\File
 */
trait HasFilesystem
{

    /**
     * @var FilesystemOperator|FileDisk
     */
    protected $filesystem;

    /**
     * Set the Filesystem object.
     *
     * @param FilesystemOperator $filesystem
     *
     * @return $this
     */
    public function setFilesystem(FilesystemOperator $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * Retrieve the Filesystem object.
     *
     * @return FilesystemOperator|FileDisk
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

}