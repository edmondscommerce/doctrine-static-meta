<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

class EntityGenerator extends AbstractGenerator
{
    public function generateEntity(string $fullyQualifiedName)
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
        }
        $this->replaceNamespace($this->projectRootNamespace . '\\' . $this->entitiesFolderName, $abstractTestPath);

        $this->parseAndCreate(
            $fullyQualifiedName . 'Test',
            $this->testSubFolderName,
            self::ENTITY_TEST_TEMPLATE_PATH,
            self::FIND_ENTITY_NAME . 'Test'
        );
    }

    protected function parseAndCreate(string $fullyQualifiedName, string $subDir, string $templatePath, string $entityFindName = self::FIND_ENTITY_NAME)
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

    }
}
