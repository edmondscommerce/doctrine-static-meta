<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

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
     * @return string - absolute path to created file
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function generateEntity(
        string $entityFullyQualifiedName
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
                    . 'Only singular entity names are allowed. '
                    . 'Please update this to ['.$singular.']'
                );
            }

            $this->createEntityTest($entityFullyQualifiedName);
            $this->createEntityRepository($entityFullyQualifiedName);

            return $this->createEntity($entityFullyQualifiedName);
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
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
                'IdFieldInterface',
                'UuidFieldInterface',
                $filePath
            );
            $this->findReplace(
                'IdFieldTrait',
                'UuidFieldTrait',
                $filePath
            );
        }

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
