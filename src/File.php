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
    public function __construct(?FilesystemOperator $filesystem = null, $path = null)
    {
        if ($path !== null) {
            $this->setPath($path);
        }
        if ($filesystem !== null) {
            $this->setFilesystem($filesystem);
        }
    }
}
