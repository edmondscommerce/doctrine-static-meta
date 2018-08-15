<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\PathHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpInterface;
use gossi\codegen\model\PhpTrait;

class EntityFieldSetter
{
    /**
     * @var CodeHelper
     */
    protected $codeHelper;
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;
    /**
     * @var PathHelper
     */
    protected $pathHelper;

    public function __construct(CodeHelper $codeHelper, NamespaceHelper $namespaceHelper, PathHelper $pathHelper)
    {
        $this->codeHelper      = $codeHelper;
        $this->namespaceHelper = $namespaceHelper;
        $this->pathHelper      = $pathHelper;
    }

    /**
     * @param string $fieldFqn
     * @param string $entityFqn
     *
     * @throws DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function setEntityHasField(string $entityFqn, string $fieldFqn): void
    {
        try {
            $entityReflection          = new \ts\Reflection\ReflectionClass($entityFqn);
            $entity                    = PhpClass::fromFile($entityReflection->getFileName());
            $entityInterfaceFqn        = $this->namespaceHelper->getEntityInterfaceFromEntityFqn($entityFqn);
            $entityInterfaceReflection = new \ts\Reflection\ReflectionClass($entityInterfaceFqn);
            $entityInterface           = PhpInterface::fromFile($entityInterfaceReflection->getFileName());
            $fieldReflection           = new \ts\Reflection\ReflectionClass($fieldFqn);
            $field                     = PhpTrait::fromFile($fieldReflection->getFileName());
            $fieldInterfaceFqn         = \str_replace(
                ['Traits', 'Trait'],
                ['Interfaces', 'Interface'],
                $fieldFqn
            );
            $fieldInterfaceReflection  = new \ts\Reflection\ReflectionClass($fieldInterfaceFqn);
            $this->checkInterfaceLooksLikeField($fieldInterfaceReflection);
            $fieldInterface = PhpInterface::fromFile($fieldInterfaceReflection->getFileName());
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Failed loading the entity or field from FQN: ' . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
        $entity->addTrait($field);
        $this->codeHelper->generate($entity, $entityReflection->getFileName());
        $entityInterface->addInterface($fieldInterface);
        $this->codeHelper->generate($entityInterface, $entityInterfaceReflection->getFileName());
    }

    protected function fieldHasFakerProvider(\ts\Reflection\ReflectionClass $fieldTraitReflection): bool
    {
        return \class_exists(
            $this->namespaceHelper->getFakerProviderFqnFromFieldTraitReflection($fieldTraitReflection)
        );
    }

    protected function updateFakerProviderArray()
    {
        $abstractTestPath = $this->pathHelper->getProjectRootDirectory() . '/tests/Entities/AbstractEntityTest.php';
        $abstractTest     = PhpClass::fromFile($abstractTestPath);
        $const            = $abstractTest->getConstant('FAKER_DATA_PROVIDERS');
        $expression       = $const->getExpression();

    }

    /**
     * @param \ts\Reflection\ReflectionClass $fieldInterfaceReflection
     */
    protected function checkInterfaceLooksLikeField(\ts\Reflection\ReflectionClass $fieldInterfaceReflection): void
    {
        $lookFor = [
            'PROP_',
            'DEFAULT_',
        ];
        $found   = [];
        $consts  = $fieldInterfaceReflection->getConstants();
        foreach (\array_keys($consts) as $name) {
            foreach ($lookFor as $key => $prefix) {
                if (\ts\stringStartsWith($name, $prefix)) {
                    $found[$key] = $prefix;
                }
            }
        }
        if ($found !== $lookFor) {
            throw new \InvalidArgumentException(
                'Field ' . $fieldInterfaceReflection->getName()
                . ' does not look like a field interface, failed to find the following const prefixes: '
                . "\n" . print_r($lookFor, true)
            );
        }
    }
}
