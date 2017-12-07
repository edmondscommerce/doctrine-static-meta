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

        $this->parseAndCreate(
            $fullyQualifiedName,
            $this->testSubFolderName,
            self::ENTITY_TEST_TEMPLATE_PATH
        );
    }

    protected function parseAndCreate(string $fullyQualifiedName, string $subDir, string $templatePath)
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
        $this->replaceEntityName($className, $filePath);
        $this->replaceNamespace($namespace, $filePath);
    }
}
