<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepositoryFactory;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Savers\AbstractEntitySpecificSaver;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpInterface;

class EntityGenerator extends AbstractGenerator
{
    /**
     * Flag to determine if a UUID primary key should be used for this entity.
     *
     * @var bool
     */
    protected $useUuidPrimaryKey = false;

    /**
     * @param string $entityFullyQualifiedName
     *
     * @param bool   $generateSpecificEntitySaver
     *
     * @return string - absolute path to created file
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function generateEntity(
        string $entityFullyQualifiedName,
        bool $generateSpecificEntitySaver = true
    ): string {
        try {
            if (false === strpos($entityFullyQualifiedName, '\\'.AbstractGenerator::ENTITIES_FOLDER_NAME.'\\')) {
                throw new \RuntimeException(
                    'Fully qualified name ['.$entityFullyQualifiedName
                    .'] does not include the Entities folder name ['
                    .AbstractGenerator::ENTITIES_FOLDER_NAME
                    .']. Please ensure you pass in the full namespace qualified entity name'
                );
            }

            $shortName = MappingHelper::getShortNameForFqn($entityFullyQualifiedName);
            $plural    = MappingHelper::getPluralForFqn($entityFullyQualifiedName);
            $singular  = MappingHelper::getSingularForFqn($entityFullyQualifiedName);

            if (\strtolower($shortName) === $plural) {
                throw new \RuntimeException(
                    'Plural entity name used ['.$plural.']. '
                    .'Only singular entity names are allowed. '
                    .'Please update this to ['.$singular.']'
                );
            }

            $this->createEntityTest($entityFullyQualifiedName);
            $this->createEntityRepository($entityFullyQualifiedName);
            $this->createEntityRepositoryFactory($entityFullyQualifiedName);
            if (true === $generateSpecificEntitySaver) {
                $this->createEntitySaver($entityFullyQualifiedName);
            }

            $this->createInterface($entityFullyQualifiedName);

            return $this->createEntity($entityFullyQualifiedName);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function createInterface(string $entityFullyQualifiedName): void
    {
        $entityInterfaceFqn = \str_replace(
            '\\'.AbstractGenerator::ENTITIES_FOLDER_NAME.'\\',
            '\\'.AbstractGenerator::ENTITY_INTERFACE_NAMESPACE.'\\',
            $entityFullyQualifiedName
        ).'Interface';

        list($className, $namespace, $subDirectories) = $this->parseFullyQualifiedName(
            $entityInterfaceFqn,
            $this->srcSubFolderName
        );

        $filePath = $this->copyTemplateAndGetPath(
            self::ENTITY_INTERFACE_TEMPLATE_PATH,
            $className,
            $subDirectories
        );

        $this->replaceName($className, $filePath, self::FIND_ENTITY_NAME.'Interface');
        $this->replaceEntityInterfaceNamespace($namespace, $filePath);
    }

    protected function createEntity(
        string $entityFullyQualifiedName
    ): string {
        list($filePath, $className, $namespace) = $this->parseAndCreate(
            $entityFullyQualifiedName,
            $this->srcSubFolderName,
            self::ENTITY_TEMPLATE_PATH
        );
        $this->replaceName($className, $filePath, static::FIND_ENTITY_NAME);
        $this->replaceEntitiesNamespace($namespace, $filePath);
        $this->replaceEntityRepositoriesNamespace($namespace, $filePath);

        if ($this->getUseUuidPrimaryKey()) {
            $this->findReplace(
                'IdFieldTrait',
                'UuidFieldTrait',
                $filePath
            );
        }

        $interfaceNamespace = \str_replace(
            '\\'.AbstractGenerator::ENTITIES_FOLDER_NAME,
            '\\'.AbstractGenerator::ENTITY_INTERFACE_NAMESPACE,
            $namespace
        );

        $this->replaceEntityInterfaceNamespace($interfaceNamespace, $filePath);

        return $filePath;
    }

    /**
     * @param string $entityFullyQualifiedName
     *
     * @throws DoctrineStaticMetaException
     */
    protected function createEntityTest(string $entityFullyQualifiedName): void
    {
        try {
            $abstractTestPath = $this->pathToProjectRoot.'/'
                                .$this->testSubFolderName
                                .'/'.AbstractGenerator::ENTITIES_FOLDER_NAME
                                .'/AbstractEntityTest.php';
            if (!$this->getFilesystem()->exists($abstractTestPath)) {
                $this->getFilesystem()->copy(self::ABSTRACT_ENTITY_TEST_TEMPLATE_PATH, $abstractTestPath);
                $this->fileCreationTransaction::setPathCreated($abstractTestPath);
                $this->findReplace(
                    self::FIND_PROJECT_NAMESPACE,
                    rtrim($this->projectRootNamespace, '\\'),
                    $abstractTestPath
                );
            }

            $phpunitBootstrapPath = $this->pathToProjectRoot.'/'
                                    .$this->testSubFolderName.'/bootstrap.php';
            if (!$this->getFilesystem()->exists($phpunitBootstrapPath)) {
                $this->getFilesystem()->copy(self::PHPUNIT_BOOTSTRAP_TEMPLATE_PATH, $phpunitBootstrapPath);
                $this->fileCreationTransaction::setPathCreated($phpunitBootstrapPath);
            }

            list($filePath, $className, $namespace) = $this->parseAndCreate(
                $entityFullyQualifiedName.'Test',
                $this->testSubFolderName,
                self::ENTITY_TEST_TEMPLATE_PATH
            );
            $this->findReplace(
                self::FIND_ENTITIES_NAMESPACE,
                $this->namespaceHelper->tidy($namespace),
                $filePath
            );

            $this->replaceName($className, $filePath, self::FIND_ENTITY_NAME.'Test');
            $this->replaceProjectNamespace($this->projectRootNamespace, $filePath);
            $this->replaceEntityRepositoriesNamespace($namespace, $filePath);
            $this->findReplace(
                'use FQNFor\AbstractEntityTest;',
                'use '.$this->namespaceHelper->tidy(
                    $this->projectRootNamespace
                    .'\\'.AbstractGenerator::ENTITIES_FOLDER_NAME
                    .'\\AbstractEntityTest;'
                ),
                $filePath
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $entityFullyQualifiedName
     *
     * @throws DoctrineStaticMetaException
     */
    protected function createEntityRepository(string $entityFullyQualifiedName): void
    {
        try {
            $abstractRepositoryPath = $this->pathToProjectRoot
                                      .'/'.$this->srcSubFolderName
                                      .'/'.AbstractGenerator::ENTITY_REPOSITORIES_FOLDER_NAME
                                      .'/AbstractEntityRepository.php';
            if (!$this->getFilesystem()->exists($abstractRepositoryPath)) {
                $this->getFilesystem()->copy(
                    self::ABSTRACT_ENTITY_REPOSITORY_TEMPLATE_PATH,
                    $abstractRepositoryPath
                );
                $this->fileCreationTransaction::setPathCreated($abstractRepositoryPath);
                $this->replaceEntityRepositoriesNamespace(
                    $this->projectRootNamespace.'\\'
                    .AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE,
                    $abstractRepositoryPath
                );
            }
            $entityRepositoryFqn = \str_replace(
                '\\'.AbstractGenerator::ENTITIES_FOLDER_NAME.'\\',
                '\\'.AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE.'\\',
                $entityFullyQualifiedName
            ).'Repository';

            list($filePath, $className, $namespace) = $this->parseAndCreate(
                $entityRepositoryFqn,
                $this->srcSubFolderName,
                self::REPOSITORIES_TEMPLATE_PATH
            );
            $this->findReplace(
                self::FIND_ENTITY_REPOSITORIES_NAMESPACE,
                $this->namespaceHelper->tidy($namespace),
                $filePath
            );

            $this->replaceName($className, $filePath, self::FIND_ENTITY_NAME.'Repository');
            $this->replaceProjectNamespace($this->projectRootNamespace, $filePath);
            $this->replaceEntityRepositoriesNamespace($namespace, $filePath);
            $this->findReplace(
                'use FQNFor\AbstractEntityRepository;',
                'use '.$this->namespaceHelper->tidy(
                    $this->projectRootNamespace
                    .'\\'.AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE
                    .'\\AbstractEntityRepository;'
                ),
                $filePath
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Create an entity repository factory
     *
     * @param string $entityFqn
     *
     * @throws DoctrineStaticMetaException
     */
    protected function createEntityRepositoryFactory(string $entityFqn)
    {
        $repositoryFactoryFqn = \str_replace(
            '\\'.AbstractGenerator::ENTITIES_FOLDER_NAME.'\\',
            AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE.'\\',
            $entityFqn
        ).'RepositoryFactory';

        $abstractRepositoryFactoryFqn = $this->projectRootNamespace
                                        .AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE
                                        .'\\AbstractEntityRepositoryFactory';

        $repositoryFactory = new PhpClass();
        $repositoryFactory
            ->setUseStatements(['\\'.$abstractRepositoryFactoryFqn])
            ->setQualifiedName($repositoryFactoryFqn)
            ->setParentClassName('AbstractEntityRepositoryFactory');

        list($className, , $subDirectories) = $this->parseFullyQualifiedName(
            $repositoryFactoryFqn,
            $this->srcSubFolderName
        );

        $filePath = $this->createSubDirectoriesAndGetPath($subDirectories);

        $this->codeHelper->generate($repositoryFactory, $filePath.'/'.$className.'.php');

        $this->createAbstractEntityRepositoryFactory();
    }

    /**
     * Create the abstract entity repository factory if it doesn't currently exist
     */
    protected function createAbstractEntityRepositoryFactory()
    {
        $abstractRepositoryFactoryPath = $this->pathToProjectRoot
                                         .'/'.$this->srcSubFolderName
                                         .'/'.AbstractGenerator::ENTITY_REPOSITORIES_FOLDER_NAME
                                         .'/AbstractEntityRepositoryFactory.php';

        if ($this->getFilesystem()->exists($abstractRepositoryFactoryPath)) {
            return;
        }

        $abstractFactoryFqn = $this->projectRootNamespace
                              .AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE
                              .'\\AbstractEntityRepositoryFactory';

        $abstractFactory = new PhpClass();
        $abstractFactory
            ->setUseStatements([AbstractEntityRepositoryFactory::class.' as DSMRepositoryFactory'])
            ->setQualifiedName($abstractFactoryFqn)
            ->setParentClassName('DSMRepositoryFactory');

        $this->codeHelper->generate($abstractFactory, $abstractRepositoryFactoryPath);
    }


    /**
     * Create an entity saver
     *
     * @param string $entityFqn
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function createEntitySaver(string $entityFqn): void
    {
        $entitySaverFqn = \str_replace(
            '\\'.AbstractGenerator::ENTITIES_FOLDER_NAME.'\\',
            AbstractGenerator::ENTITY_SAVERS_NAMESPACE.'\\',
            $entityFqn
        ).'Saver';


        $entitySaver = new PhpClass();
        $entitySaver
            ->setQualifiedName($entitySaverFqn)
            ->setParentClassName('\\'.AbstractEntitySpecificSaver::class)
            ->setInterfaces(
                [
                    PhpInterface::fromFile(__DIR__.'/../../Entity/Savers/EntitySaverInterface.php'),
                ]
            );

        list($className, , $subDirectories) = $this->parseFullyQualifiedName(
            $entitySaverFqn,
            $this->srcSubFolderName
        );

        $filePath = $this->createSubDirectoriesAndGetPath($subDirectories);

        $this->codeHelper->generate($entitySaver, $filePath.'/'.$className.'.php');
    }

    /**
     * @throws DoctrineStaticMetaException
     */
    protected function parseAndCreate(
        string $fullyQualifiedName,
        string $subDir,
        string $templatePath
    ): array {
        try {
            list($className, $namespace, $subDirectories) = $this->parseFullyQualifiedName(
                $fullyQualifiedName,
                $subDir
            );
            $filePath = $this->copyTemplateAndGetPath(
                $templatePath,
                $className,
                $subDirectories
            );

            return [$filePath, $className, $this->namespaceHelper->tidy($namespace)];
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    public function setUseUuidPrimaryKey(bool $useUuidPrimaryKey)
    {
        $this->useUuidPrimaryKey = $useUuidPrimaryKey;

        return $this;
    }

    public function getUseUuidPrimaryKey()
    {
        return $this->useUuidPrimaryKey;
    }
}
