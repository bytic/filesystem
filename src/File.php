<?php
namespace Nip\Filesystem;

use League\Flysystem\FilesystemOperator;

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
    public function __construct(FilesystemOperator $filesystem = null, $path = null)
    {
        $this->parseNameFromPath($path);
        $this->path = $path;
        $this->setFilesystem($filesystem);
    }
}
