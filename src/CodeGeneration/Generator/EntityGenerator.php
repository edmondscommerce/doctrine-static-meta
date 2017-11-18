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
            $subDirectories
        );
        $this->replaceEntityName($className, $filePath);
        $this->replaceNamespace($namespace, $filePath);
    }
}
