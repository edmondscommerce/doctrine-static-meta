<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\UuidIdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Repositories\AbstractEntityRepository;
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
    protected $useUuidPrimaryKey = true;

    /**
     * @param string $entityFqn
     *
     * @param bool   $generateSpecificEntitySaver
     *
     * @return string - absolute path to created file
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function generateEntity(
        string $entityFqn,
        bool $generateSpecificEntitySaver = false
    ): string {
        try {
            if (false === \ts\stringContains($entityFqn, '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\')) {
                throw new \RuntimeException(
                    'Fully qualified name [' . $entityFqn
                    . '] does not include the Entities folder name ['
                    . AbstractGenerator::ENTITIES_FOLDER_NAME
                    . ']. Please ensure you pass in the full namespace qualified entity name'
                );
            }

            $shortName = MappingHelper::getShortNameForFqn($entityFqn);
            $plural    = MappingHelper::getPluralForFqn($entityFqn);
            $singular  = MappingHelper::getSingularForFqn($entityFqn);

            if (\strtolower($shortName) === $plural) {
                throw new \RuntimeException(
                    'Plural entity name used [' . $plural . ']. '
                    . 'Only singular entity names are allowed. '
                    . 'Please update this to [' . $singular . ']'
                );
            }

            $this->createEntityTest($entityFqn);
            $this->createEntityFixture($entityFqn);
            $this->createEntityRepository($entityFqn);
            $this->createEntityFactory($entityFqn);
            if (true === $generateSpecificEntitySaver) {
                $this->createEntitySaver($entityFqn);
            }

            $this->createInterface($entityFqn);

            return $this->createEntity($entityFqn);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $entityFullyQualifiedName
     *
     * @throws DoctrineStaticMetaException
     */
    protected function createEntityTest(string $entityFullyQualifiedName): void
    {
        try {
            $abstractTestPath = $this->pathToProjectRoot . '/'
                                . $this->testSubFolderName
                                . '/' . AbstractGenerator::ENTITIES_FOLDER_NAME
                                . '/AbstractEntityTest.php';
            if (!$this->getFilesystem()->exists($abstractTestPath)) {
                $this->getFilesystem()->copy(self::ABSTRACT_ENTITY_TEST_TEMPLATE_PATH, $abstractTestPath);
                $this->fileCreationTransaction::setPathCreated($abstractTestPath);
                $this->findAndReplaceHelper->findReplace(
                    self::FIND_PROJECT_NAMESPACE,
                    rtrim($this->projectRootNamespace, '\\'),
                    $abstractTestPath
                );
            }

            $phpunitBootstrapPath = $this->pathToProjectRoot . '/'
                                    . $this->testSubFolderName . '/bootstrap.php';
            if (!$this->getFilesystem()->exists($phpunitBootstrapPath)) {
                $this->getFilesystem()->copy(self::PHPUNIT_BOOTSTRAP_TEMPLATE_PATH, $phpunitBootstrapPath);
                $this->fileCreationTransaction::setPathCreated($phpunitBootstrapPath);
            }

            list($filePath, $className, $namespace) = $this->parseAndCreate(
                $entityFullyQualifiedName . 'Test',
                $this->testSubFolderName,
                self::ENTITY_TEST_TEMPLATE_PATH
            );
            $this->findAndReplaceHelper->findReplace(
                self::FIND_ENTITIES_NAMESPACE,
                $this->namespaceHelper->tidy($namespace),
                $filePath
            );

            $this->findAndReplaceHelper->replaceName($className, $filePath, self::FIND_ENTITY_NAME . 'Test');
            $this->findAndReplaceHelper->replaceProjectNamespace($this->projectRootNamespace, $filePath);
            $this->findAndReplaceHelper->replaceEntityRepositoriesNamespace($namespace, $filePath);
            $this->findAndReplaceHelper->findReplace(
                'use FQNFor\AbstractEntityTest;',
                'use ' . $this->namespaceHelper->tidy(
                    $this->projectRootNamespace
                    . '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME
                    . '\\AbstractEntityTest;'
                ),
                $filePath
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
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
            $filePath = $this->pathHelper->copyTemplateAndGetPath(
                $this->pathToProjectRoot,
                $templatePath,
                $className,
                $subDirectories
            );

            return [$filePath, $className, $this->namespaceHelper->tidy($namespace)];
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    protected function createEntityFixture(string $entityFullyQualifiedName): void
    {

        list($filePath, $className, $namespace) = $this->parseAndCreate(
            $this->namespaceHelper->getFixtureFqnFromEntityFqn($entityFullyQualifiedName),
            $this->testSubFolderName,
            self::ENTITY_FIXTURE_TEMPLATE_PATH
        );
        $this->findAndReplaceHelper->findReplace(
            'TemplateNamespace\Assets\EntityFixtures',
            $this->namespaceHelper->tidy($namespace),
            $filePath
        );
        $this->findAndReplaceHelper->replaceName($className, $filePath, self::FIND_ENTITY_NAME . 'Fixture');
        $this->findAndReplaceHelper->replaceProjectNamespace($this->projectRootNamespace, $filePath);
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
                                      . '/' . $this->srcSubFolderName
                                      . '/' . AbstractGenerator::ENTITY_REPOSITORIES_FOLDER_NAME
                                      . '/AbstractEntityRepository.php';
            if (!$this->getFilesystem()->exists($abstractRepositoryPath)) {
                $this->getFilesystem()->copy(
                    self::ABSTRACT_ENTITY_REPOSITORY_TEMPLATE_PATH,
                    $abstractRepositoryPath
                );
                $this->fileCreationTransaction::setPathCreated($abstractRepositoryPath);
                $this->findAndReplaceHelper->replaceEntityRepositoriesNamespace(
                    $this->projectRootNamespace . '\\'
                    . AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE,
                    $abstractRepositoryPath
                );
            }
            $entityRepositoryFqn = \str_replace(
                '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\',
                '\\' . AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE . '\\',
                $entityFullyQualifiedName
            ) . 'Repository';

            list($filePath, $className, $namespace) = $this->parseAndCreate(
                $entityRepositoryFqn,
                $this->srcSubFolderName,
                self::REPOSITORIES_TEMPLATE_PATH
            );
            $this->findAndReplaceHelper->findReplace(
                self::FIND_ENTITY_REPOSITORIES_NAMESPACE,
                $this->namespaceHelper->tidy($namespace),
                $filePath
            );
            $classInterfaceNamespace = \str_replace(
                '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\',
                '\\' . AbstractGenerator::ENTITY_INTERFACE_NAMESPACE . '\\',
                $entityFullyQualifiedName
            ) . 'Interface';
            $classInterface          = preg_replace('#Repository$#', 'Interface', $className);

            $this->findAndReplaceHelper->replaceEntityInterfaceNamespace($classInterfaceNamespace, $filePath);
            $this->findAndReplaceHelper->replaceName($className, $filePath, self::FIND_ENTITY_NAME . 'Repository');
            $this->findAndReplaceHelper->replaceProjectNamespace($this->projectRootNamespace, $filePath);
            $this->findAndReplaceHelper->replaceEntityRepositoriesNamespace($namespace, $filePath);
            $this->findAndReplaceHelper->replaceName($classInterface, $filePath, self::FIND_ENTITY_NAME . 'Interface');
            $this->findAndReplaceHelper->findReplace(
                'use FQNFor\AbstractEntityRepository;',
                'use ' . $this->namespaceHelper->tidy(
                    $this->projectRootNamespace
                    . '\\' . AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE
                    . '\\AbstractEntityRepository;'
                ),
                $filePath
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param string $entityFullyQualifiedName
     *
     * @throws DoctrineStaticMetaException
     */
    protected function createEntityFactory(string $entityFullyQualifiedName): void
    {
        try {
            $abstractFactoryPath = $this->pathToProjectRoot
                                   . '/' . $this->srcSubFolderName
                                   . '/' . AbstractGenerator::ENTITY_FACTORIES_FOLDER_NAME
                                   . '/AbstractEntityFactory.php';
            if (!$this->getFilesystem()->exists($abstractFactoryPath)) {
                $this->getFilesystem()->copy(
                    self::ABSTRACT_ENTITY_FACTORY_TEMPLATE_PATH,
                    $abstractFactoryPath
                );
                $this->fileCreationTransaction::setPathCreated($abstractFactoryPath);
                $this->findAndReplaceHelper->replaceEntityRepositoriesNamespace(
                    $this->projectRootNamespace . '\\'
                    . AbstractGenerator::ENTITY_FACTORIES_NAMESPACE,
                    $abstractFactoryPath
                );
            }
            $entityFactoryFqn = \str_replace(
                '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\',
                '\\' . AbstractGenerator::ENTITY_FACTORIES_NAMESPACE . '\\',
                $entityFullyQualifiedName
            ) . 'Factory';

            list($filePath, $className, $namespace) = $this->parseAndCreate(
                $entityFactoryFqn,
                $this->srcSubFolderName,
                self::FACTORIES_TEMPLATE_PATH
            );
            list($entityShortName, ,) = $this->parseFullyQualifiedName($entityFullyQualifiedName);
            $this->findAndReplaceHelper->findReplace(
                self::FIND_ENTITY_FACTORIES_NAMESPACE,
                $this->namespaceHelper->tidy($namespace),
                $filePath
            );
            $classInterfaceNamespace = \str_replace(
                '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\',
                '\\' . AbstractGenerator::ENTITY_INTERFACE_NAMESPACE . '\\',
                $entityFullyQualifiedName
            ) . 'Interface';
            $classInterface          = preg_replace('#Factory$#', 'Interface', $className);
            $this->findAndReplaceHelper->replaceEntitiesNamespace($entityFullyQualifiedName, $filePath);
            $this->findAndReplaceHelper->findReplace('EntityFqn', $entityFullyQualifiedName, $filePath);
            $this->findAndReplaceHelper->replaceName($entityShortName, $filePath, self::FIND_ENTITY_NAME);
            $this->findAndReplaceHelper->replaceEntityInterfaceNamespace($classInterfaceNamespace, $filePath);
            $this->findAndReplaceHelper->replaceName($className, $filePath, self::FIND_ENTITY_NAME . 'Factory');
            $this->findAndReplaceHelper->replaceProjectNamespace($this->projectRootNamespace, $filePath);
            $this->findAndReplaceHelper->replaceEntityRepositoriesNamespace($namespace, $filePath);
            $this->findAndReplaceHelper->replaceName($classInterface, $filePath, self::FIND_ENTITY_NAME . 'Interface');
            $this->findAndReplaceHelper->findReplace(
                'use FQNFor\AbstractEntityFactory;',
                'use ' . $this->namespaceHelper->tidy(
                    $this->projectRootNamespace
                    . '\\' . AbstractGenerator::ENTITY_FACTORIES_NAMESPACE
                    . '\\AbstractEntityFactory;'
                ),
                $filePath
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Exception in ' . __METHOD__ . ': ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
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
            '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\',
            AbstractGenerator::ENTITY_SAVERS_NAMESPACE . '\\',
            $entityFqn
        ) . 'Saver';


        $entitySaver = new PhpClass();
        $entitySaver
            ->setQualifiedName($entitySaverFqn)
            ->setParentClassName('\\' . AbstractEntitySpecificSaver::class)
            ->setInterfaces(
                [
                    PhpInterface::fromFile(__DIR__ . '/../../Entity/Savers/EntitySaverInterface.php'),
                ]
            );

        list($className, , $subDirectories) = $this->parseFullyQualifiedName(
            $entitySaverFqn,
            $this->srcSubFolderName
        );

        $filePath = $this->createSubDirectoriesAndGetPath($subDirectories);

        $this->codeHelper->generate($entitySaver, $filePath . '/' . $className . '.php');
    }

    /**
     * @param string $entityFullyQualifiedName
     *
     * @throws DoctrineStaticMetaException
     * @throws \ReflectionException
     */
    protected function createInterface(string $entityFullyQualifiedName): void
    {
        $entityInterfaceFqn = \str_replace(
            '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME . '\\',
            '\\' . AbstractGenerator::ENTITY_INTERFACE_NAMESPACE . '\\',
            $entityFullyQualifiedName
        ) . 'Interface';

        list($className, $namespace, $subDirectories) = $this->parseFullyQualifiedName(
            $entityInterfaceFqn,
            $this->srcSubFolderName
        );

        $filePath = $this->pathHelper->copyTemplateAndGetPath(
            $this->pathToProjectRoot,
            self::ENTITY_INTERFACE_TEMPLATE_PATH,
            $className,
            $subDirectories
        );

        $this->findAndReplaceHelper->replaceName($className, $filePath, self::FIND_ENTITY_NAME . 'Interface');
        $this->findAndReplaceHelper->replaceEntityInterfaceNamespace($namespace, $filePath);
    }

    protected function createEntity(
        string $entityFullyQualifiedName
    ): string {
        list($filePath, $className, $namespace) = $this->parseAndCreate(
            $entityFullyQualifiedName,
            $this->srcSubFolderName,
            self::ENTITY_TEMPLATE_PATH
        );
        $this->findAndReplaceHelper->replaceName($className, $filePath, static::FIND_ENTITY_NAME);
        $this->findAndReplaceHelper->replaceEntitiesNamespace($namespace, $filePath);
        $this->findAndReplaceHelper->replaceEntityRepositoriesNamespace(
            \str_replace(
                '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME,
                '\\' . AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE,
                $namespace
            ),
            $filePath
        );

        $this->findAndReplaceHelper->findReplace(
            'use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;',
            $this->useUuidPrimaryKey ?
                'use DSM\Fields\Traits\PrimaryKey\UuidFieldTrait;' :
                'use DSM\Fields\Traits\PrimaryKey\IntegerIdFieldTrait;',
            $filePath
        );


        $interfaceNamespace = \str_replace(
            '\\' . AbstractGenerator::ENTITIES_FOLDER_NAME,
            '\\' . AbstractGenerator::ENTITY_INTERFACE_NAMESPACE,
            $namespace
        );

        $this->findAndReplaceHelper->replaceEntityInterfaceNamespace($interfaceNamespace, $filePath);

        return $filePath;
    }

    public function getUseUuidPrimaryKey(): bool
    {
        return $this->useUuidPrimaryKey;
    }

    public function setUseUuidPrimaryKey(bool $useUuidPrimaryKey): self
    {
        $this->useUuidPrimaryKey = $useUuidPrimaryKey;

        return $this;
    }

    /**
     * Create the abstract entity repository factory if it doesn't currently exist
     */
    protected function createAbstractEntityRepositoryFactory(): void
    {
        $abstractRepositoryFactoryPath = $this->pathToProjectRoot
                                         . '/' . $this->srcSubFolderName
                                         . '/' . AbstractGenerator::ENTITY_REPOSITORIES_FOLDER_NAME
                                         . '/AbstractEntityRepositoryFactory.php';

        if ($this->getFilesystem()->exists($abstractRepositoryFactoryPath)) {
            return;
        }

        $abstractFactoryFqn = $this->projectRootNamespace
                              . AbstractGenerator::ENTITY_REPOSITORIES_NAMESPACE
                              . '\\AbstractEntityRepositoryFactory';

        $abstractFactory = new PhpClass();
        $abstractFactory
            ->setUseStatements([AbstractEntityRepository::class . ' as DSMRepositoryFactory'])
            ->setQualifiedName($abstractFactoryFqn)
            ->setParentClassName('DSMRepositoryFactory');

        $this->codeHelper->generate($abstractFactory, $abstractRepositoryFactoryPath);
    }
}
