<?php
namespace Nip\Filesystem;

use League\Flysystem\FilesystemInterface;

/**
 * Class File
 * @package Nip\Filesystem
 */
class File
{
    use File\HasFilesystem;
    use File\HasInformation;
    use File\HasOperations;
    use File\HasPath;

    /**
     * @inheritdoc
     */
    public function __construct(FilesystemInterface $filesystem = null, $path = null)
    {
        $this->parseNameFromPath($path);
        $this->path = $path;
        $this->setFilesystem($filesystem);
    }
}
