<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractGenerator
{
    public const TEMPLATE_PATH = __DIR__.'/../../../codeTemplates';

    public const ENTITY_TEMPLATE_PATH = self::TEMPLATE_PATH.'/src/Entities/TemplateEntity.php';

    public const ENTITY_TEST_TEMPLATE_PATH = self::TEMPLATE_PATH.'/tests/Entities/TemplateEntityTest.php';

    public const ABSTRACT_ENTITY_TEST_TEMPLATE_PATH = self::TEMPLATE_PATH.'/tests/Entities/AbstractEntityTest.php';

    public const PHPUNIT_BOOTSTRAP_TEMPLATE_PATH = self::TEMPLATE_PATH.'/tests/bootstrap.php';

    public const RELATIONS_TEMPLATE_PATH = self::TEMPLATE_PATH.'/src/EntityRelations/TemplateEntity';

    public const REPOSITORIES_TEMPLATE_PATH = self::TEMPLATE_PATH
                                              .'/src/EntityRepositories/TemplateEntityRepository.php';

    public const ABSTRACT_ENTITY_REPOSITORY_TEMPLATE_PATH = self::TEMPLATE_PATH
                                                            .'/src/EntityRepositories/AbstractEntityRepository.php';

    public const FIND_ENTITY_NAME = 'TemplateEntity';

    public const FIND_ENTITY_NAME_PLURAL = 'TemplateEntities';

    public const FIND_ENTITIES_NAMESPACE = 'TemplateNamespace\\Entities';

    public const FIND_ENTITY_RELATIONS_NAMESPACE = 'TemplateNamespace\\EntityRelations';

    public const FIND_ENTITY_REPOSITORIES_NAMESPACE = 'TemplateNamespace\\EntityRepositories';

    public const ENTITIES_FOLDER_NAME = 'Entities';

    public const ENTITY_RELATIONS_FOLDER_NAME = 'EntityRelations';

    public const ENTITY_REPOSITORIES_FOLDER_NAME = 'EntityRepositories';

    /**
     * @var string
     */
    protected $projectRootNamespace = '';

    /**
     * @var string
     */
    protected $pathToProjectRoot = '';

    /**
     * @var string
     */
    protected $srcSubFolderName = AbstractCommand::DEFAULT_SRC_SUBFOLDER;

    /**
     * @var string
     */
    protected $testSubFolderName = AbstractCommand::DEFAULT_TEST_SUBFOLDER;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var FileCreationTransaction
     */
    protected $fileCreationTransaction;

    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;

    public function __construct(
        Filesystem $filesystem,
        FileCreationTransaction $fileCreationTransaction,
        NamespaceHelper $namespaceHelper,
        Config $config
    ) {
        $this->fileSystem              = $filesystem;
        $this->fileCreationTransaction = $fileCreationTransaction;
        $this->namespaceHelper         = $namespaceHelper;
        $this->setProjectRootNamespace($this->namespaceHelper->getProjectRootNamespaceFromComposerJson());
        $this->setPathToProjectRoot($config::getProjectRootDirectory());
    }

    /**
     * @param string $projectRootNamespace
     *
     * @return $this
     */
    public function setProjectRootNamespace(string $projectRootNamespace): AbstractGenerator
    {
        $this->projectRootNamespace = $projectRootNamespace;

        return $this;
    }

    /**
     * @param string $pathToProjectRoot
     *
     * @return $this
     */
    public function setPathToProjectRoot(string $pathToProjectRoot): AbstractGenerator
    {
        $this->pathToProjectRoot = $pathToProjectRoot;

        return $this;
    }

    /**
     * @param string $srcSubFolderName
     *
     * @return $this
     */
    public function setSrcSubFolderName(string $srcSubFolderName): AbstractGenerator
    {
        $this->srcSubFolderName = $srcSubFolderName;

        return $this;
    }

    /**
     * @param string $testSubFolderName
     *
     * @return $this
     */
    public function setTestSubFolderName(string $testSubFolderName): AbstractGenerator
    {
        $this->testSubFolderName = $testSubFolderName;

        return $this;
    }


    protected function getFilesystem(): Filesystem
    {
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
     * @throws DoctrineStaticMetaException
     */
    protected function parseFullyQualifiedName(string $fqn, string $srcOrTestSubFolder = null): array
    {
        if (null === $srcOrTestSubFolder) {
            $srcOrTestSubFolder = $this->srcSubFolderName;
        }

        return $this->namespaceHelper->parseFullyQualifiedName($fqn, $srcOrTestSubFolder, $this->projectRootNamespace);
    }

    /**
     * @param array $subDirectories
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    protected function createSubDirectoriesAndGetPath(array $subDirectories): string
    {
        $filesystem = $this->getFilesystem();
        $path       = $this->pathToProjectRoot;
        if (!$filesystem->exists($path)) {
            throw new DoctrineStaticMetaException("path to project root $path does not exist");
        }
        foreach ($subDirectories as $sd) {
            $path .= "/$sd";
            try {
                $filesystem->mkdir($path);
            } catch (\Exception $e) {
                throw new DoctrineStaticMetaException(
                    'Exception in '.__METHOD__.': '.$e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
        }

        return realpath($path);
    }

    /**
     * @param string $templatePath
     * @param string $destPath
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    protected function copyTemplateDirectoryAndGetPath(
        string $templatePath,
        string $destPath
    ): string {
        $filesystem       = $this->getFilesystem();
        $realTemplatePath = realpath($templatePath);
        if (false === $realTemplatePath) {
            throw new DoctrineStaticMetaException('path '.$templatePath.' does not exist');
        }
        $relativeDestPath = $filesystem->makePathRelative($destPath, $this->pathToProjectRoot);
        $subDirectories   = explode('/', $relativeDestPath);
        $path             = $this->createSubDirectoriesAndGetPath($subDirectories);
        $filesystem->mirror($realTemplatePath, $path);
        $this->fileCreationTransaction::setPathCreated($path);

        return $path;
    }

    /**
     * @param string $templatePath
     * @param string $destinationFileName
     * @param array  $subDirectories
     *
     * @return string
     * @throws DoctrineStaticMetaException
     */
    protected function copyTemplateAndGetPath(
        string $templatePath,
        string $destinationFileName,
        array $subDirectories
    ): string {
        try {
            $path = $this->createSubDirectoriesAndGetPath($subDirectories);
            if (false === strpos($destinationFileName, '.php')) {
                $destinationFileName = "$destinationFileName.php";
            }
            $filePath = "$path/$destinationFileName";
            $this->getFilesystem()->copy($templatePath, $filePath, true);
            $this->fileCreationTransaction::setPathCreated($filePath);

            return $filePath;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $find
     * @param string $replace
     * @param string $filePath
     *
     * @return AbstractGenerator
     */
    protected function findReplace(
        string $find,
        string $replace,
        string $filePath
    ): AbstractGenerator {
        $contents = file_get_contents($filePath);
        $contents = str_replace($find, $replace, $contents);
        file_put_contents($filePath, $contents);

        return $this;
    }

    /**
     * @param string $find
     * @param string $replace
     * @param string $filePath
     *
     * @return AbstractGenerator
     */
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

    /**
     * @param string $replacement
     * @param string $filePath
     * @param string $findName
     *
     * @return AbstractGenerator
     */
    protected function replaceEntityName(
        string $replacement,
        string $filePath,
        $findName = self::FIND_ENTITY_NAME
    ): AbstractGenerator {
        $this->findReplace($findName, $replacement, $filePath);
        $this->findReplace(lcfirst($findName), lcfirst($replacement), $filePath);

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return AbstractGenerator
     */
    protected function replacePluralEntityName(string $replacement, string $filePath): AbstractGenerator
    {
        $this->findReplace(self::FIND_ENTITY_NAME_PLURAL, $replacement, $filePath);
        $this->findReplace(lcfirst(self::FIND_ENTITY_NAME_PLURAL), lcfirst($replacement), $filePath);

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return AbstractGenerator
     */
    protected function replaceEntityNamespace(string $replacement, string $filePath): AbstractGenerator
    {
        $this->findReplace(self::FIND_ENTITIES_NAMESPACE, $replacement, $filePath);

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return AbstractGenerator
     */
    protected function replaceEntityRelationsNamespace(string $replacement, string $filePath): AbstractGenerator
    {
        $this->findReplace(self::FIND_ENTITY_RELATIONS_NAMESPACE, $replacement, $filePath);

        return $this;
    }


    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return AbstractGenerator
     */
    protected function replaceEntityRepositoriesNamespace(string $replacement, string $filePath): AbstractGenerator
    {
        $this->findReplace(self::FIND_ENTITY_REPOSITORIES_NAMESPACE, $replacement, $filePath);

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
            strpos(
                $filePath,
                $this->srcSubFolderName
            )
            + strlen($this->srcSubFolderName)
            + 1
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
            throw new DoctrineStaticMetaException(
                'Namespace replace count is '.$count.', should be 1 when updating file: '.$filePath
            );
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
        $moveTo      = \dirname($path).'/'.$newBasename;
        if ($moveTo === $path) {
            return $path;
        }
        if (is_dir($moveTo) || file_exists($moveTo)) {
            throw new DoctrineStaticMetaException(
                "Error trying to move:\n[$path]\n to \n[$moveTo]\ndestination already exists"
            );
        }
        try {
            $this->getFilesystem()->rename($path, $moveTo);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }

        return $moveTo;
    }

    protected function getPathFromNameAndSubDirs(string $name, array $subDirectories): string
    {
        $path = realpath($this->pathToProjectRoot).'/'.implode('/', $subDirectories).'/'.$name.'.php';

        return $path;
    }
}
