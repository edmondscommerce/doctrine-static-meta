<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem;

class Directory extends AbstractFilesystemItem
{
    /**
     * This is the name of the path type. It should be overriden in child classes.
     *
     * @var string
     */
    protected const PATH_TYPE = 'directory';

    protected function doCreate(): void
    {
        if (!mkdir($this->path, $this->createModeOctal, true) && !is_dir($this->path)) {
            // @codeCoverageIgnoreStart
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->path));
            // @codeCoverageIgnoreEnd
        }
    }

}