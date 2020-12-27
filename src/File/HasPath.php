<?php

namespace Nip\Filesystem\File;

/**
 * Trait HasPath
 * @package Nip\Filesystem\File
 */
trait HasPath
{

    /**
     * @var
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var
     */
    protected $url;


    /**
     * Set the entree path.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath(string $path)
    {
        $this->parseNameFromPath($path);
        $this->path = $path;

        return $this;
    }

    /**
     * Retrieve the entree path.
     *
     * @return string path
     */
    public function getPath(): string
    {
        if (!$this->path) {
            $this->initPath();
        }
        return $this->path;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setFileName(string $name): self
    {
        $path_parts = pathinfo($this->getPath());
        $path_parts['filename'] = $name;
        $this->setPath(
            $path_parts['dirname']
            . '/' . $path_parts['filename'] . '.' . $path_parts['extension']
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getPathFolder(): string
    {
        return '/';
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if (!$this->name) {
            $this->initName();
        }
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    protected function initName()
    {
        $this->name = $this->getDefaultName();
    }

    /**
     * @return string
     */
    public function getDefaultName(): string
    {
        return 'file';
    }


    /**
     * @return mixed
     */
    public function getUrl()
    {
        if (!$this->url) {
            $this->initUrl();
        }
        return $this->url;
    }

    protected function initUrl()
    {
        $this->url = $this->getFilesystem()->getUrl($this->getPath());
    }

    /**
     * @param $path
     */
    protected function parseNameFromPath($path)
    {
        $name = pathinfo($path, PATHINFO_BASENAME);
        $this->setName($name);
    }

    /**
     * @return void
     */
    protected function initPath()
    {
        $this->setPath($this->getPathFolder() . $this->getName());
    }
}
