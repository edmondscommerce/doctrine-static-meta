<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Command\AbstractCommand;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\Config;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractGenerator
{
    public const TEMPLATE_PATH = __DIR__ . '/../../../codeTemplates';

    public const ENTITIES_FOLDER_NAME = 'Entities';

    public const ENTITY_FOLDER_NAME = 'Entity';

    public const ENTITY_RELATIONS_FOLDER_NAME = '/' . self::ENTITY_FOLDER_NAME . '/Relations/';

    public const ENTITY_REPOSITORIES_FOLDER_NAME = '/' . self::ENTITY_FOLDER_NAME . '/Repositories/';

    public const ENTITY_FIELDS_FOLDER_NAME = '/' . self::ENTITY_FOLDER_NAME . '/Fields/';

    public const ENTITY_SAVERS_FOLDER_NAME = '/' . self::ENTITY_FOLDER_NAME . '/Savers/';

    public const ENTITY_INTERFACES_FOLDER_NAME = '/' . self::ENTITY_FOLDER_NAME . '/Interfaces/';

    public const ENTITY_TEMPLATE_PATH = self::TEMPLATE_PATH . '/src/' . self::ENTITIES_FOLDER_NAME
                                        . '/TemplateEntity.php';

    public const ENTITY_INTERFACE_TEMPLATE_PATH = self::TEMPLATE_PATH . '/src/' . self::ENTITY_INTERFACES_FOLDER_NAME
                                                  . '/TemplateEntityInterface.php';

    public const ENTITY_TEST_TEMPLATE_PATH = self::TEMPLATE_PATH . '/tests/' . self::ENTITIES_FOLDER_NAME
                                             . '/TemplateEntityTest.php';

    public const ABSTRACT_ENTITY_TEST_TEMPLATE_PATH = self::TEMPLATE_PATH . '/tests/' . self::ENTITIES_FOLDER_NAME
                                                      . '/AbstractEntityTest.php';

    public const PHPUNIT_BOOTSTRAP_TEMPLATE_PATH = self::TEMPLATE_PATH . '/tests/bootstrap.php';

    public const RELATIONS_TEMPLATE_PATH = self::TEMPLATE_PATH . '/src/' . self::ENTITY_RELATIONS_FOLDER_NAME
                                           . '/TemplateEntity';

    public const REPOSITORIES_TEMPLATE_PATH = self::TEMPLATE_PATH
                                              . '/src/' . self::ENTITY_REPOSITORIES_FOLDER_NAME
                                              . '/TemplateEntityRepository.php';

    public const ABSTRACT_ENTITY_REPOSITORY_TEMPLATE_PATH = self::TEMPLATE_PATH
                                                            . '/src/' . self::ENTITY_REPOSITORIES_FOLDER_NAME
                                                            . '/AbstractEntityRepository.php';

    public const FIELD_TRAIT_TEMPLATE_PATH = self::TEMPLATE_PATH . '/src/'
                                             . self::ENTITY_FIELDS_FOLDER_NAME
                                             . '/Traits/'
                                             . self::FIND_ENTITY_FIELD_NAME . 'FieldTrait.php';

    public const FIELD_INTERFACE_TEMPLATE_PATH = self::TEMPLATE_PATH . '/src/'
                                                 . self::ENTITY_FIELDS_FOLDER_NAME
                                                 . '/Interfaces/'
                                                 . self::FIND_ENTITY_FIELD_NAME . 'FieldInterface.php';

    public const FIND_ENTITY_NAME = 'TemplateEntity';

    public const FIND_ENTITY_NAME_PLURAL = 'TemplateEntities';

    public const FIND_PROJECT_NAMESPACE = 'TemplateNamespace';

    public const FIND_ENTITIES_NAMESPACE = self::FIND_PROJECT_NAMESPACE . '\\Entities';

    public const FIND_ENTITY_NAMESPACE = self::FIND_PROJECT_NAMESPACE . '\\Entity';

    public const ENTITY_RELATIONS_NAMESPACE = '\\Entity\\Relations';

    public const FIND_ENTITY_RELATIONS_NAMESPACE = self::FIND_PROJECT_NAMESPACE . self::ENTITY_RELATIONS_NAMESPACE;

    public const ENTITY_REPOSITORIES_NAMESPACE = '\\Entity\\Repositories';

    public const ENTITY_SAVERS_NAMESPACE = '\\Entity\\Savers';

    public const FIND_ENTITY_REPOSITORIES_NAMESPACE = self::FIND_PROJECT_NAMESPACE .
                                                      self::ENTITY_REPOSITORIES_NAMESPACE;

    public const ENTITY_INTERFACE_NAMESPACE = '\\Entity\\Interfaces';

    public const FIND_ENTITY_INTERFACE_NAMESPACE = self::FIND_PROJECT_NAMESPACE . self::ENTITY_INTERFACE_NAMESPACE;

    public const FIND_ENTITY_FIELD_NAME = 'TemplateFieldName';

    public const ENTITY_FIELD_NAMESPACE = '\\Entity\\Fields';

    public const ENTITY_FIELD_TRAIT_NAMESPACE = self::ENTITY_FIELD_NAMESPACE . '\\Traits';

    public const ENTITY_FIELD_INTERFACE_NAMESPACE = self::ENTITY_FIELD_NAMESPACE . '\\Interfaces';

    public const FIND_FIELD_TRAIT_NAMESPACE = self::FIND_PROJECT_NAMESPACE . self::ENTITY_FIELD_TRAIT_NAMESPACE;

    public const FIND_FIELD_INTERFACE_NAMESPACE = self::FIND_PROJECT_NAMESPACE . self::ENTITY_FIELD_INTERFACE_NAMESPACE;

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
    /**
     * @var PathHelper
     */
    protected $pathHelper;
    /**
     * @var FindAndReplaceHelper
     */
    protected $findAndReplaceHelper;

    public function __construct(
        Filesystem $filesystem,
        FileCreationTransaction $fileCreationTransaction,
        NamespaceHelper $namespaceHelper,
        Config $config,
        CodeHelper $codeHelper,
        PathHelper $pathHelper,
        FindAndReplaceHelper $findAndReplaceHelper
    ) {
        $this->fileSystem              = $filesystem;
        $this->fileCreationTransaction = $fileCreationTransaction;
        $this->namespaceHelper         = $namespaceHelper;
        $this->setProjectRootNamespace($this->namespaceHelper->getProjectRootNamespaceFromComposerJson());
        $this->setPathToProjectRoot($config::getProjectRootDirectory());
        $this->codeHelper           = $codeHelper;
        $this->pathHelper           = $pathHelper;
        $this->findAndReplaceHelper = $findAndReplaceHelper;
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
            throw new \RuntimeException('Invalid path to project root ' . $pathToProjectRoot);
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
        return $this->pathHelper->createSubDirectoriesAndGetPath($this->pathToProjectRoot, $subDirectories);
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
        return $this->pathHelper->copyTemplateDirectoryAndGetPath(
            $this->pathToProjectRoot,
            $templatePath,
            $destPath
        );
    }
}
