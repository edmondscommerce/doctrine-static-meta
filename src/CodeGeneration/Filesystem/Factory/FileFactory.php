<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\Factory;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Creation\CreatorInterface;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Filesystem\File;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

class FileFactory
{
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string
     */
    private $projectRootNamespace;

    /**
     * @var string
     */
    private $projectRootDirectory;

    public function __construct(NamespaceHelper $namespaceHelper, Config $config)
    {
        $this->namespaceHelper      = $namespaceHelper;
        $this->projectRootNamespace = $this->namespaceHelper->getProjectRootNamespaceFromComposerJson();
        $this->projectRootDirectory = $config::getProjectRootDirectory();
    }

    public function setProjectRootNamespace(string $projectRootNamespace): self
    {
        $this->projectRootNamespace = $projectRootNamespace;

        return $this;
    }

    /**
     * Create a new file object from an existing file path
     *
     * @param string $path
     *
     * @return File
     * @throws DoctrineStaticMetaException
     */
    public function createFromExistingPath(string $path): File
    {
        $file = new File($path);
        if (false === $file->exists()) {
            throw new DoctrineStaticMetaException('File does not exist at ' . $path);
        }

        return $file;
    }

    /**
     * Create a new file object from a fully qualified name, may or may not exist
     *
     * @param string $fqn
     * @param string $srcOrTestSubFolder
     *
     * @return File
     * @throws DoctrineStaticMetaException
     */
    public function createFromFqn(string $fqn, $srcOrTestSubFolder = CreatorInterface::SRC_FOLDER): File
    {
        list($className, , $subDirectories) = $this->namespaceHelper->parseFullyQualifiedName(
            $fqn,
            $srcOrTestSubFolder,
            $this->projectRootNamespace
        );
        $path = $this->projectRootDirectory
                . '/' . implode('/', $subDirectories) . '/' . $className . '.php';

        return new File($path);
    }

    /**
     * @param string $projectRootDirectory
     *
     * @return FileFactory
     */
    public function setProjectRootDirectory(string $projectRootDirectory): FileFactory
    {
        $this->projectRootDirectory = $projectRootDirectory;

        return $this;
    }
}