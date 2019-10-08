<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem;

use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use SplFileObject;

use function dirname;

class File extends AbstractFilesystemItem
{
    protected const PATH_TYPE = 'file';

    /**
     * The permissions to be set on newly created files
     *
     * @var int
     */
    protected $createMode = 0644;

    /**
     * @var string|null
     */
    protected $contents;

    /**
     * Stores an instance fo the SplFileObject object for the file.
     *
     * @var SplFileObject
     */
    protected $splFileObject;

    /**
     * @var Directory
     */
    private $directory;

    public function removeIfExists(): self
    {
        if ($this->exists()) {
            unlink($this->path);
        }

        return $this;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath(string $path): self
    {
        parent::setPath($path);
        $this->directory = new Directory(dirname($path));

        return $this;
    }

    /**
     * @return string
     */
    public function getContents(): ?string
    {
        return $this->contents;
    }

    /**
     * @param string $contents
     *
     * @return File
     */
    public function setContents(string $contents): File
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * @return File
     * @throws DoctrineStaticMetaException
     */
    public function putContents(): self
    {
        $this->assertExists();
        \ts\file_put_contents($this->path, $this->contents);

        return $this;
    }

    /**
     * @return File
     * @throws DoctrineStaticMetaException
     */
    public function loadContents(): self
    {
        $this->assertExists();
        $this->contents = \ts\file_get_contents($this->path);

        return $this;
    }

    /**
     * Provide an SplFileObject object, asserting that the path exists
     *
     * @return SplFileObject
     * @throws DoctrineStaticMetaException
     */
    public function getSplFileObject(): SplFileObject
    {
        $this->assertExists();
        if (null !== $this->splFileObject && $this->path === $this->splFileObject->getRealPath()) {
            return $this->splFileObject;
        }
        $this->splFileObject = new SplFileObject($this->path);

        return $this->splFileObject;
    }

    /**
     * @return Directory
     */
    public function getDirectory(): Directory
    {
        return $this->directory;
    }

    /**
     * This is the specific creation logic for the concrete filesystem type.
     */
    protected function doCreate(): void
    {
        $this->directory->assertExists();
        \ts\file_put_contents($this->path, '');
    }

    protected function isCorrectType(): bool
    {
        return $this->createSplFileInfo()->isFile();
    }
}
