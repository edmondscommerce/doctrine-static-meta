<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;

class EntityGenerator extends AbstractGenerator
{
    /**
     * @param string $fullyQualifiedName
     *
     * @param string $transactionClass - FQN for transaction class
     *
     * @return string - absolute path to created file
     * @throws DoctrineStaticMetaException
     */
    public function generateEntity(
        string $fullyQualifiedName,
        string $transactionClass = FileCreationTransaction::class
    ): string {
        try {
            if (false === strpos($fullyQualifiedName, $this->entitiesFolderName)) {
                throw new \RuntimeException(
                    'Fully qualified name ['.$fullyQualifiedName
                    .'] does not include the Entities folder name ['
                    .$this->entitiesFolderName
                    .']. Please ensure you pass in the full namespace qualified entity name'
                );
            }
            //create entity
            $entityFilePath = $this->parseAndCreate(
                $fullyQualifiedName,
                $this->srcSubFolderName,
                self::ENTITY_TEMPLATE_PATH
            );

            //create entity test, abstract test first
            $abstractTestPath = $this->pathToProjectSrcRoot.'/'
                .$this->testSubFolderName.'/'.$this->entitiesFolderName.'/AbstractEntityTest.php';
            if (!$this->getFilesystem()->exists($abstractTestPath)) {
                $this->getFilesystem()->copy(self::ABSTRACT_ENTITY_TEST_TEMPLATE_PATH, $abstractTestPath);
                $transactionClass::setPathCreated($abstractTestPath);
            }
            $this->replaceEntityNamespace(
                $this->projectRootNamespace.'\\'
                .$this->entitiesFolderName,
                $abstractTestPath
            );

            $phpunitBootstrapPath = $this->pathToProjectSrcRoot.'/'
                .$this->testSubFolderName.'/bootstrap.php';
            if (!$this->getFilesystem()->exists($phpunitBootstrapPath)) {
                $this->getFilesystem()->copy(self::PHPUNIT_BOOTSTRAP_TEMPLATE_PATH, $phpunitBootstrapPath);
                $transactionClass::setPathCreated($phpunitBootstrapPath);
            }

            $this->parseAndCreate(
                $fullyQualifiedName.'Test',
                $this->testSubFolderName,
                self::ENTITY_TEST_TEMPLATE_PATH,
                self::FIND_ENTITY_NAME.'Test'
            );

            return $entityFilePath;
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
            $this->findReplace(
                'use FQNFor\AbstractEntityTest;',
                'use '.$this->namespaceHelper->tidy(
                    $this->projectRootNamespace.'\\'.$this->entitiesFolderName.'\\AbstractEntityTest;'
                ),
                $filePath
            );

            return $filePath;
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException('Exception in '.__METHOD__.': '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
