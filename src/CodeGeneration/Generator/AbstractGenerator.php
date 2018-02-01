<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractGenerator
{
    const TEMPLATE_PATH = __DIR__.'/../../../codeTemplates';

    const ENTITY_TEMPLATE_PATH = self::TEMPLATE_PATH.'/src/Entities/TemplateEntity.php';

    const ENTITY_TEST_TEMPLATE_PATH = self::TEMPLATE_PATH.'/tests/Entities/TemplateEntityTest.php';

    const ABSTRACT_ENTITY_TEST_TEMPLATE_PATH = self::TEMPLATE_PATH.'/tests/Entities/AbstractEntityTest.php';

    const PHPUNIT_BOOTSTRAP_TEMPLATE_PATH = self::TEMPLATE_PATH.'/tests/bootstrap.php';

    const RELATIONS_TEMPLATE_PATH = self::TEMPLATE_PATH.'/src/Entities/Relations/TemplateEntity';

    const FIND_ENTITY_NAME = 'TemplateEntity';

    const FIND_ENTITY_NAME_PLURAL = 'TemplateEntities';

    const FIND_NAMESPACE = 'TemplateNamespace\\Entities';

    /**
     * @var string
     */
    protected $projectRootNamespace = '';

    /**
     * @var string
     */
    protected $pathToProjectSrcRoot = '';

    /**
     * @var string
     */
    protected $entitiesFolderName = '';

    /**
     * @var string
     */
    protected $srcSubFolderName = '';

    /**
     * @var string
     */
    protected $testSubFolderName = '';

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    public function __construct(
        string $projectRootNamespace,
        string $pathToProjectSrcRoot,
        string $entitiesFolderName = AbstractCommand::DEFAULT_ENTITIES_ROOT_FOLDER,
        string $srcSubFolderName = AbstractCommand::DEFAULT_SRC_SUBFOLDER,
        string $testSubFolderName = AbstractCommand::DEFAULT_TEST_SUBFOLDER
    ) {
        $this->projectRootNamespace = $projectRootNamespace;
        $this->pathToProjectSrcRoot = $pathToProjectSrcRoot;
        $this->entitiesFolderName   = $entitiesFolderName;
        $this->srcSubFolderName     = $srcSubFolderName;
        $this->testSubFolderName    = $testSubFolderName;
    }

    protected function getFilesystem(): Filesystem
    {
        if (null === $this->fileSystem) {
            $this->fileSystem = new Filesystem();
        }

        return $this->fileSystem;
    }

    /**
     * From the fully qualified name, parse out:
     *  - class name,
     *  - namespace
     *  - the namespace parts not including the project root namespace
     *
     * @param string $fqn
     *
     * @param string $srcOrTestSubFolder
     *
     * @return array [$className,$namespace,$subDirectories]
     */
    protected function parseFullyQualifiedName(string $fqn, string $srcOrTestSubFolder = null)
    {
        if (null === $srcOrTestSubFolder) {
            $srcOrTestSubFolder = $this->srcSubFolderName;
        }

        return (new NamespaceHelper())->parseFullyQualifiedName($fqn, $srcOrTestSubFolder, $this->projectRootNamespace);
    }

    protected function createSubDirectoriesAndGetPath(array $subDirectories): string
    {
        $filesystem = $this->getFilesystem();
        $path       = $this->pathToProjectSrcRoot;
        if (!$filesystem->exists($path)) {
            throw new DoctrineStaticMetaException("path to project root $path does not exist");
        }
        foreach ($subDirectories as $sd) {
            $path .= "/$sd";
            $filesystem->mkdir($path);
        }

        return realpath($path);
    }

    protected function copyTemplateDirectoryAndGetPath(
        string $templatePath,
        string $destPath,
        string $fileTransactionClass = FileCreationTransaction::class
    ): string {
        $filesystem       = $this->getFilesystem();
        $templatePath     = realpath($templatePath);
        $relativeDestPath = $filesystem->makePathRelative($destPath, $this->pathToProjectSrcRoot);
        $subDirectories   = explode('/', $relativeDestPath);
        $path             = $this->createSubDirectoriesAndGetPath($subDirectories);
        $filesystem->mirror($templatePath, $path);
        $fileTransactionClass::setPathCreated($path);

        return $path;
    }

    protected function copyTemplateAndGetPath(
        string $templatePath,
        string $destinationFileName,
        array $subDirectories,
        string $fileTransactionClass = FileCreationTransaction::class
    ): string {
        $path = $this->createSubDirectoriesAndGetPath($subDirectories);
        if (false === strpos($destinationFileName, '.php')) {
            $destinationFileName = "$destinationFileName.php";
        }
        $filePath = "$path/$destinationFileName";
        $this->getFilesystem()->copy($templatePath, $filePath, true);
        $fileTransactionClass::setPathCreated($filePath);

        return $filePath;
    }

    protected function findReplace(
        string $find,
        string $replace,
        string $filePath,
        bool $regex = false
    ): AbstractGenerator {
        $contents = file_get_contents($filePath);
        $contents = str_replace($find, $replace, $contents);
        file_put_contents($filePath, $contents);

        return $this;
    }

    protected function findReplaceRegex(
        string $find,
        string $replace,
        string $filePath
    ): AbstractGenerator {
        $contents = file_get_contents($filePath);
        $contents = preg_replace($find, $replace, $contents);
        file_put_contents($filePath, $contents);

        return $this;
    }

    protected function replaceEntityName(
        string $replacement,
        string $filePath,
        $findName = self::FIND_ENTITY_NAME
    ): AbstractGenerator {
        $this->findReplace($findName, $replacement, $filePath);
        $this->findReplace(lcfirst($findName), lcfirst($replacement), $filePath);

        return $this;
    }

    protected function replacePluralEntityName(string $replacement, string $filePath): AbstractGenerator
    {
        $this->findReplace(self::FIND_ENTITY_NAME_PLURAL, $replacement, $filePath);
        $this->findReplace(lcfirst(self::FIND_ENTITY_NAME_PLURAL), lcfirst($replacement), $filePath);

        return $this;
    }

    protected function replaceNamespace(string $replacement, string $filePath): AbstractGenerator
    {
        $this->findReplace(self::FIND_NAMESPACE, $replacement, $filePath);

        return $this;
    }

    /**
     * Totally replace the defined namespace in a class/trait
     * with a namespace calculated from the path of the file
     *
     * @param string $filePath
     *
     * @return AbstractGenerator
     * @throws DoctrineStaticMetaException
     */
    protected function setNamespaceFromPath(string $filePath): AbstractGenerator
    {
        $pathForNamespace = substr(
            $filePath,
            (
                strpos(
                    $filePath,
                    $this->srcSubFolderName
                )
                + strlen($this->srcSubFolderName)
                + 1
            )
        );
        $pathForNamespace = substr($pathForNamespace, 0, strrpos($pathForNamespace, '/'));
        $namespaceToSet   = $this->projectRootNamespace
                            .'\\'.implode(
                                '\\',
                                explode(
                                    '/',
                                    $pathForNamespace
                                )
                            );
        $contents         = file_get_contents($filePath);
        $contents         = preg_replace(
            '%namespace[^:]+?;%',
            "namespace $namespaceToSet;",
            $contents,
            1,
            $count
        );
        if ($count !== 1) {
            throw new DoctrineStaticMetaException('Namespace replace count is '.$count.', should be 1 when updating file: '.$filePath);
        }
        file_put_contents($filePath, $contents);

        return $this;
    }

    /**
     * Move the basename of a path to the find/replaced version
     *
     * Then return the updated path
     *
     * @param string $find
     * @param string $replace
     * @param string $path
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    protected function renamePathBasename(string $find, string $replace, string $path): string
    {
        $basename    = basename($path);
        $newBasename = str_replace($find, $replace, $basename);
        $moveTo      = dirname($path).'/'.$newBasename;
        if ($moveTo === $path) {
            return $path;
        }
        if (is_dir($moveTo) || file_exists($moveTo)) {
            throw new DoctrineStaticMetaException("Error trying to move:\n[$path]\n to \n[$moveTo]\ndestination already exists");
        }
        $this->getFilesystem()->rename($path, $moveTo);

        return $moveTo;
    }

    protected function getPathFromNameAndSubDirs(string $name, array $subDirectories): string
    {
        $path = realpath($this->pathToProjectSrcRoot).'/'.implode('/', $subDirectories).'/'.$name.'.php';

        return $path;
    }
}
