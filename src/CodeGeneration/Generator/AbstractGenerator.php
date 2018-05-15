<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use Doctrine\Common\Util\Inflector;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractGenerator
{
    public const TEMPLATE_PATH = __DIR__.'/../../../codeTemplates';

    public const ENTITIES_FOLDER_NAME = 'Entities';

    public const ENTITY_FOLDER_NAME = 'Entity';

    public const ENTITY_RELATIONS_FOLDER_NAME = '/'.self::ENTITY_FOLDER_NAME.'/Relations/';

    public const ENTITY_REPOSITORIES_FOLDER_NAME = '/'.self::ENTITY_FOLDER_NAME.'/Repositories/';

    public const ENTITY_FIELDS_FOLDER_NAME = '/'.self::ENTITY_FOLDER_NAME.'/Fields/';

    public const ENTITY_SAVERS_FOLDER_NAME = '/'.self::ENTITY_FOLDER_NAME.'/Savers/';

    public const ENTITY_INTERFACES_FOLDER_NAME = '/'.self::ENTITY_FOLDER_NAME.'/Interfaces/';

    public const ENTITY_TEMPLATE_PATH = self::TEMPLATE_PATH.'/src/'.self::ENTITIES_FOLDER_NAME
                                        .'/TemplateEntity.php';

    public const ENTITY_INTERFACE_TEMPLATE_PATH = self::TEMPLATE_PATH.'/src/'.self::ENTITY_INTERFACES_FOLDER_NAME
                                                  .'/TemplateEntityInterface.php';

    public const ENTITY_TEST_TEMPLATE_PATH = self::TEMPLATE_PATH.'/tests/'.self::ENTITIES_FOLDER_NAME
                                             .'/TemplateEntityTest.php';

    public const ABSTRACT_ENTITY_TEST_TEMPLATE_PATH = self::TEMPLATE_PATH.'/tests/'.self::ENTITIES_FOLDER_NAME
                                                      .'/AbstractEntityTest.php';

    public const PHPUNIT_BOOTSTRAP_TEMPLATE_PATH = self::TEMPLATE_PATH.'/tests/bootstrap.php';

    public const RELATIONS_TEMPLATE_PATH = self::TEMPLATE_PATH.'/src/'.self::ENTITY_RELATIONS_FOLDER_NAME
                                           .'/TemplateEntity';

    public const REPOSITORIES_TEMPLATE_PATH = self::TEMPLATE_PATH
                                              .'/src/'.self::ENTITY_REPOSITORIES_FOLDER_NAME
                                              .'/TemplateEntityRepository.php';

    public const ABSTRACT_ENTITY_REPOSITORY_TEMPLATE_PATH = self::TEMPLATE_PATH
                                                            .'/src/'.self::ENTITY_REPOSITORIES_FOLDER_NAME
                                                            .'/AbstractEntityRepository.php';

    public const FIELD_TRAIT_TEMPLATE_PATH = self::TEMPLATE_PATH.'/src/'
                                             .self::ENTITY_FIELDS_FOLDER_NAME
                                             .'/Traits/'
                                             .self::FIND_ENTITY_FIELD_NAME.'FieldTrait.php';

    public const FIELD_INTERFACE_TEMPLATE_PATH = self::TEMPLATE_PATH.'/src/'
                                                 .self::ENTITY_FIELDS_FOLDER_NAME
                                                 .'/Interfaces/'
                                                 .self::FIND_ENTITY_FIELD_NAME.'FieldInterface.php';

    public const FIND_ENTITY_NAME = 'TemplateEntity';

    public const FIND_ENTITY_NAME_PLURAL = 'TemplateEntities';

    public const FIND_PROJECT_NAMESPACE = 'TemplateNamespace';

    public const FIND_ENTITIES_NAMESPACE = self::FIND_PROJECT_NAMESPACE.'\\Entities';

    public const FIND_ENTITY_NAMESPACE = self::FIND_PROJECT_NAMESPACE.'\\Entity';

    public const ENTITY_RELATIONS_NAMESPACE = '\\Entity\\Relations';

    public const FIND_ENTITY_RELATIONS_NAMESPACE = self::FIND_PROJECT_NAMESPACE.self::ENTITY_RELATIONS_NAMESPACE;

    public const ENTITY_REPOSITORIES_NAMESPACE = '\\Entity\\Repositories';

    public const ENTITY_SAVERS_NAMESPACE = '\\Entity\\Savers';

    public const FIND_ENTITY_REPOSITORIES_NAMESPACE = self::FIND_PROJECT_NAMESPACE.self::ENTITY_REPOSITORIES_NAMESPACE;

    public const ENTITY_INTERFACE_NAMESPACE = '\\Entity\\Interfaces';

    public const FIND_ENTITY_INTERFACE_NAMESPACE = self::FIND_PROJECT_NAMESPACE.self::ENTITY_INTERFACE_NAMESPACE;

    public const FIND_ENTITY_FIELD_NAME = 'TemplateFieldName';

    public const ENTITY_FIELD_NAMESPACE = '\\Entity\\Fields';

    public const ENTITY_FIELD_TRAIT_NAMESPACE = self::ENTITY_FIELD_NAMESPACE.'\\Traits';

    public const ENTITY_FIELD_INTERFACE_NAMESPACE = self::ENTITY_FIELD_NAMESPACE.'\\Interfaces';

    public const FIND_FIELD_TRAIT_NAMESPACE = self::FIND_PROJECT_NAMESPACE.self::ENTITY_FIELD_TRAIT_NAMESPACE;

    public const FIND_FIELD_INTERFACE_NAMESPACE = self::FIND_PROJECT_NAMESPACE.self::ENTITY_FIELD_INTERFACE_NAMESPACE;

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
    /**
     * @var CodeHelper
     */
    protected $codeHelper;

    public function __construct(
        Filesystem $filesystem,
        FileCreationTransaction $fileCreationTransaction,
        NamespaceHelper $namespaceHelper,
        Config $config,
        CodeHelper $codeHelper
    ) {
        $this->fileSystem              = $filesystem;
        $this->fileCreationTransaction = $fileCreationTransaction;
        $this->namespaceHelper         = $namespaceHelper;
        $this->setProjectRootNamespace($this->namespaceHelper->getProjectRootNamespaceFromComposerJson());
        $this->setPathToProjectRoot($config::getProjectRootDirectory());
        $this->codeHelper = $codeHelper;
    }

    /**
     * @param string $projectRootNamespace
     *
     * @return $this
     */
    public function setProjectRootNamespace(string $projectRootNamespace): AbstractGenerator
    {
        $this->projectRootNamespace = rtrim($projectRootNamespace, '\\');

        return $this;
    }

    /**
     * @param string $pathToProjectRoot
     *
     * @return $this
     * @throws \RuntimeException
     */
    public function setPathToProjectRoot(string $pathToProjectRoot): AbstractGenerator
    {
        $realPath = \realpath($pathToProjectRoot);
        if (false === $realPath) {
            throw new \RuntimeException('Invalid path to project root '.$pathToProjectRoot);
        }
        $this->pathToProjectRoot = $realPath;

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
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function replaceName(
        string $replacement,
        string $filePath,
        $findName = self::FIND_ENTITY_NAME
    ): AbstractGenerator {
        $this->findReplace($findName, $replacement, $filePath);
        $this->findReplace(\lcfirst($findName), \lcfirst($replacement), $filePath);
        $this->findReplace(\strtoupper($findName), \strtoupper($replacement), $filePath);
        $this->findReplace(
            \strtoupper(Inflector::tableize($findName)),
            \strtoupper(Inflector::tableize($replacement)),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return AbstractGenerator
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function replacePluralName(string $replacement, string $filePath): AbstractGenerator
    {
        $this->findReplace(self::FIND_ENTITY_NAME_PLURAL, $replacement, $filePath);
        $this->findReplace(\lcfirst(self::FIND_ENTITY_NAME_PLURAL), \lcfirst($replacement), $filePath);
        $this->findReplace(\strtoupper(self::FIND_ENTITY_NAME_PLURAL), \strtoupper($replacement), $filePath);
        $this->findReplace(
            \strtoupper(Inflector::tableize(self::FIND_ENTITY_NAME_PLURAL)),
            \strtoupper(Inflector::tableize($replacement)),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return AbstractGenerator
     * @throws \RuntimeException
     */
    protected function replaceEntitiesNamespace(string $replacement, string $filePath): AbstractGenerator
    {
        if (false === strpos($replacement, '\\Entities')) {
            throw new \RuntimeException('$replacement '.$replacement.' does not contain \\Entities\\');
        }
        $this->findReplace(
            self::FIND_ENTITIES_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return AbstractGenerator
     * @throws \RuntimeException
     */
    protected function replaceEntityNamespace(string $replacement, string $filePath): AbstractGenerator
    {
        if (false === strpos($replacement, '\\Entity')) {
            throw new \RuntimeException('$replacement '.$replacement.' does not contain \\Entity\\');
        }
        $this->findReplace(
            self::FIND_ENTITY_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return AbstractGenerator
     * @throws \RuntimeException
     */
    protected function replaceFieldTraitNamespace(string $replacement, string $filePath): AbstractGenerator
    {
        if (false === strpos($replacement, self::ENTITY_FIELD_TRAIT_NAMESPACE)) {
            throw new \RuntimeException(
                '$replacement '.$replacement.' does not contain '.self::ENTITY_FIELD_TRAIT_NAMESPACE
            );
        }
        $this->findReplace(
            self::FIND_FIELD_TRAIT_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return AbstractGenerator
     * @throws \RuntimeException
     */
    protected function replaceFieldInterfaceNamespace(string $replacement, string $filePath): AbstractGenerator
    {
        if (false === strpos($replacement, self::ENTITY_FIELD_INTERFACE_NAMESPACE)) {
            throw new \RuntimeException(
                '$replacement '.$replacement.' does not contain '.self::ENTITY_FIELD_INTERFACE_NAMESPACE
            );
        }
        $this->findReplace(
            self::FIND_FIELD_INTERFACE_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return AbstractGenerator
     */
    protected function replaceProjectNamespace(string $replacement, string $filePath): AbstractGenerator
    {
        $this->findReplace(
            self::FIND_PROJECT_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

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
        $this->findReplace(
            self::FIND_ENTITY_REPOSITORIES_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

        return $this;
    }

    /**
     * @param string $replacement
     * @param string $filePath
     *
     * @return AbstractGenerator
     */
    protected function replaceEntityInterfaceNamespace(string $replacement, string $filePath): AbstractGenerator
    {
        $this->findReplace(
            self::FIND_ENTITY_INTERFACE_NAMESPACE,
            $this->namespaceHelper->tidy($replacement),
            $filePath
        );

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
