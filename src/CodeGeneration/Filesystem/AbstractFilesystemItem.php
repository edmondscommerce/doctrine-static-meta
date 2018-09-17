<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem;

use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

abstract class AbstractFilesystemItem
{
    /**
     * This is the name of the path type. It should be overriden in child classes.
     *
     * @var string
     */
    protected const PATH_TYPE = 'path';

    /**
     * This is the path as configured for the filesystem item. It in no way means the item exists there, it is simply
     * where we expect it to exist if/when it has been created
     *
     * @var string
     */
    protected $path;

    /**
     * What permissions should be set to created filesystem items?
     *
     * @var int
     */
    protected $createModeOctal = 0777;

    /**
     * A string representation of the octal number
     *
     * @var string
     */
    protected $createModeOctalString = '0777';

    /**
     * Stores an instance fo the SplFileInfo object for the path item.
     *
     * @var \SplFileInfo
     */
    protected $splFileInfo;

    /**
     * @param string $path - the path where we would expect the item to exist if/when it has been created
     */
    public function __construct(string $path = null)
    {
        if (null !== $path) {
            $this->setPath($path);
        }
    }

    /**
     * Create the filesystem item, asserting that it does not already exist
     *
     * @return static
     * @throws DoctrineStaticMetaException
     */
    public function create()
    {
        $this->assertPathIsSet();
        $this->assertNotExists();
        $this->doCreate();
        $this->setPermissions();

        return $this;
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    protected function assertPathIsSet(): void
    {
        if (null === $this->path) {
            throw new DoctrineStaticMetaException('$this->path is not set');
        }
    }

    /**
     * Throw an Exception if there is already something that exists at the path
     *
     * @throws DoctrineStaticMetaException
     */
    protected function assertNotExists(): void
    {
        if (true === $this->exists()) {
            throw new DoctrineStaticMetaException(static::PATH_TYPE . ' already exists at path ' . $this->path);
        }
    }

    /**
     * Check if something exists at the real path
     *
     * @return bool
     * @throws DoctrineStaticMetaException
     */
    public function exists(): bool
    {
        $realPath = \realpath($this->path);
        if (false === $realPath) {
            return false;
        }
        $this->path = $realPath;

        return true;
    }

    /**
     * This is the specific creation logic for the concrete filesystem type.
     */
    abstract protected function doCreate(): void;

    private function setPermissions()
    {
        chmod($this->path, $this->createModeOctal);
    }

    /**
     * Get the path to the filesystem item as it is currently set in this object
     *
     * @return null|string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Set the string path to the filesystem item
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Provide an SplFileInfo object, asserting that the path exists
     *
     * @return \SplFileInfo
     * @throws DoctrineStaticMetaException
     */
    public function getSplFileInfo(): \SplFileInfo
    {
        if (null !== $this->splFileInfo && $this->path === $this->splFileInfo->getRealPath()) {
            return $this->splFileInfo;
        }
        $this->assertExists();
        $this->splFileInfo = new \SplFileInfo($this->path);

        return $this->splFileInfo;
    }

    protected function assertExists(): void
    {
        $this->assertPathIsSet();
        if (false === $this->exists()) {
            throw new DoctrineStaticMetaException(static::PATH_TYPE . ' does not exist at path ' . $this->path);
        }
    }

    /**
     * @param int $createMode
     *
     * @return static
     */
    public function setCreateMode(int $createMode): self
    {
        $this->createModeOctal       = $createMode;
        $this->createModeOctalString = decoct($createMode);

        return $this;
    }
}