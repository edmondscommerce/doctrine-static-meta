<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

class EntityGenerator extends AbstractGenerator
{
    /**
     * @param string $entityFullyQualifiedName
     *
     * @return string - absolute path to created file
     * @throws DoctrineStaticMetaException
     */
    public function generateEntity(
        string $entityFullyQualifiedName
    ): string {
        try {
            if (false === strpos($entityFullyQualifiedName, $this->entitiesFolderName)) {
                throw new \RuntimeException(
                    'Fully qualified name ['.$entityFullyQualifiedName
                    .'] does not include the Entities folder name ['
                    .$this->entitiesFolderName
                    .']. Please ensure you pass in the full namespace qualified entity name'
                );
            }
            $entityFilePath = $this->parseAndCreate(
                $entityFullyQualifiedName,
                $this->srcSubFolderName,
                self::ENTITY_TEMPLATE_PATH
            );

            $this->createEntityTest($entityFullyQualifiedName);

            $this->createEntityRepository($entityFullyQualifiedName);

            return $entityFilePath;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
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
            $abstractTestPath = $this->pathToProjectRoot.'/'
                                .$this->testSubFolderName.'/'.$this->entitiesFolderName.'/AbstractEntityTest.php';
            if (!$this->getFilesystem()->exists($abstractTestPath)) {
                $this->getFilesystem()->copy(self::ABSTRACT_ENTITY_TEST_TEMPLATE_PATH, $abstractTestPath);
                $this->fileCreationTransaction::setPathCreated($abstractTestPath);
                $this->replaceEntityNamespace(
                    $this->projectRootNamespace.'\\'
                    .$this->entitiesFolderName,
                    $abstractTestPath
                );

            }

            $phpunitBootstrapPath = $this->pathToProjectRoot.'/'
                                    .$this->testSubFolderName.'/bootstrap.php';
            if (!$this->getFilesystem()->exists($phpunitBootstrapPath)) {
                $this->getFilesystem()->copy(self::PHPUNIT_BOOTSTRAP_TEMPLATE_PATH, $phpunitBootstrapPath);
                $this->fileCreationTransaction::setPathCreated($phpunitBootstrapPath);
            }

            $this->parseAndCreate(
                $entityFullyQualifiedName.'Test',
                $this->testSubFolderName,
                self::ENTITY_TEST_TEMPLATE_PATH,
                self::FIND_ENTITY_NAME.'Test'
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
                                      .'/'.$this->entityRepositoriesFolderName.'/AbstractEntityRepository.php';
            if (!$this->getFilesystem()->exists($abstractRepositoryPath)) {
                $this->getFilesystem()->copy(
                    self::ABSTRACT_ENTITY_REPOSITORY_TEMPLATE_PATH,
                    $abstractRepositoryPath
                );
                $this->fileCreationTransaction::setPathCreated($abstractRepositoryPath);
                $this->replaceEntityRepositoriesNamespace(
                    $this->projectRootNamespace.'\\'
                    .$this->entityRepositoriesFolderName,
                    $abstractRepositoryPath
                );
            }
            $entityRepositoryFqn = \str_replace(
                                       $this->entitiesFolderName,
                                       $this->entityRepositoriesFolderName,
                                       $entityFullyQualifiedName
                                   ).'Repository';

            $this->parseAndCreate(
                $entityRepositoryFqn,
                $this->srcSubFolderName,
                self::REPOSITORIES_TEMPLATE_PATH,
                self::FIND_ENTITY_NAME.'Repository'
            );
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $fullyQualifiedName
     * @param string $subDir
     * @param string $templatePath
     * @param string $entityFindName
     *
     * @return string - absolute path to created file
     * @throws DoctrineStaticMetaException
     */
    protected function parseAndCreate(
        string $fullyQualifiedName,
        string $subDir,
        string $templatePath,
        string $entityFindName = self::FIND_ENTITY_NAME
    ): string {
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

            $this->replaceEntityName($className, $filePath, $entityFindName);
            $this->replaceEntityNamespace($namespace, $filePath);
            $this->replaceEntityRelationsNamespace($namespace, $filePath);
            $this->replaceEntityRepositoriesNamespace($namespace, $filePath);
            $this->findReplace(
                'use FQNFor\AbstractEntityTest;',
                'use '.$this->namespaceHelper->tidy(
                    $this->projectRootNamespace.'\\'.$this->entitiesFolderName.'\\AbstractEntityTest;'
                ),
                $filePath
            );
            $this->findReplace(
                'use FQNFor\AbstractEntityRepository;',
                'use '.$this->namespaceHelper->tidy(
                    $this->projectRootNamespace
                    .'\\'.$this->entityRepositoriesFolderName
                    .'\\AbstractEntityRepository;'
                ),
                $filePath
            );

            return $filePath;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
