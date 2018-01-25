<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

class EntityGenerator extends AbstractGenerator
{
    /**
     * @param string $fullyQualifiedName
     *
     * @param string $transactionClass - FQN for transaction class
     *
     * @return string - absolute path to created file
     * @throws \Exception
     */
    public function generateEntity(string $fullyQualifiedName, string $transactionClass = FileCreationTransaction::class)
    {
        //create entity
        $this->parseAndCreate(
            $fullyQualifiedName,
            $this->srcSubFolderName,
            self::ENTITY_TEMPLATE_PATH
        );

        //create entity test, abstract test first
        $abstractTestPath = $this->pathToProjectSrcRoot . '/'
            . $this->testSubFolderName . '/' . $this->entitiesFolderName . '/AbstractEntityTest.php';
        if (!$this->getFilesystem()->exists($abstractTestPath)) {
            $this->getFilesystem()->copy(self::ABSTRACT_ENTITY_TEST_TEMPLATE_PATH, $abstractTestPath);
            $transactionClass::setPathCreated($abstractTestPath);
        }
        $this->replaceNamespace($this->projectRootNamespace . '\\' . $this->entitiesFolderName, $abstractTestPath);

        return $this->parseAndCreate(
            $fullyQualifiedName . 'Test',
            $this->testSubFolderName,
            self::ENTITY_TEST_TEMPLATE_PATH,
            self::FIND_ENTITY_NAME . 'Test'
        );
    }

    /**
     * @param string $fullyQualifiedName
     * @param string $subDir
     * @param string $templatePath
     * @param string $entityFindName
     *
     * @return string - absolute path to created file
     */
    protected function parseAndCreate(
        string $fullyQualifiedName,
        string $subDir,
        string $templatePath,
        string $entityFindName = self::FIND_ENTITY_NAME
    ): string
    {
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
        $this->replaceNamespace($namespace, $filePath);
        $this->findReplace(
            'use FQNFor\AbstractEntityTest;',
            'use ' . $this->projectRootNamespace . '\\' . $this->entitiesFolderName . '\\AbstractEntityTest;',
            $filePath
        );
        return $filePath;

    }
}
