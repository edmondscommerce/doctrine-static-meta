<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

class EntityGenerator extends AbstractGenerator
{
    public function generateEntity(string $fullyQualifiedName)
    {
        list($className, $namespace, $subDirectories) = $this->parseFullyQualifiedName($fullyQualifiedName);
        $filePath = $this->copyTemplateAndGetPath(
            self::ENTITY_TEMPLATE_PATH,
            $className,
            $subDirectories,
            $this->srcSubFolderName
        );
        $this->replaceEntityName($className, $filePath);
        $this->replaceNamespace($namespace, $filePath);

        $filePath = $this->copyTemplateAndGetPath(
            self::ENTITY_TEST_TEMPLATE_PATH,
            $className,
            $subDirectories,
            $this->testSubFolderName
        );
        $this->replaceEntityName($className, $filePath);
        $this->replaceNamespace($namespace, $filePath);
        
        $abstractTestPath = $this->pathToProjectSrcRoot . '/'
            . $this->testSubFolderName . '/' . $this->entitiesFolderName . '/AbstractEntityTest.php';
        if (!$this->getFilesystem()->exists($abstractTestPath)) {
            $this->getFilesystem()->copy(self::ABSTRACT_ENTITY_TEST_TEMPLATE_PATH, $abstractTestPath);
        }
    }
}
