<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\AbstractGenerator;

class ReflectionHelper
{

    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;

    public function __construct(NamespaceHelper $namespaceHelper)
    {
        $this->namespaceHelper = $namespaceHelper;
    }

    /**
     * @param \ts\Reflection\ReflectionClass $fieldTraitReflection
     *
     * @return string
     */
    public function getFakerProviderFqnFromFieldTraitReflection(\ts\Reflection\ReflectionClass $fieldTraitReflection
    ): string {
        return \str_replace(
            [
                '\\Traits\\',
                'FieldTrait',
            ],
            [
                '\\FakerData\\',
                'FakerData',
            ],
            $fieldTraitReflection->getName()
        );
    }

    /**
     * Work out the entity namespace root from a single entity reflection object.
     *
     * @param \ts\Reflection\ReflectionClass $entityReflection
     *
     * @return string
     */
    public function getEntityNamespaceRootFromEntityReflection(
        \ts\Reflection\ReflectionClass $entityReflection
    ): string {
        return $this->namespaceHelper->tidy(
            $this->namespaceHelper->getNamespaceRootToDirectoryFromFqn(
                $entityReflection->getName(),
                AbstractGenerator::ENTITIES_FOLDER_NAME
            )
        );
    }
}