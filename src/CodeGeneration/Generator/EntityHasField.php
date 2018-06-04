<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpInterface;
use gossi\codegen\model\PhpTrait;

class EntityHasField
{
    /**
     * @var CodeHelper
     */
    protected $codeHelper;

    public function __construct(CodeHelper $codeHelper)
    {
        $this->codeHelper = $codeHelper;
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
            $entityReflection         = new \ReflectionClass($entityFqn);
            $entity                   = PhpClass::fromFile($entityReflection->getFileName());
            $fieldReflection          = new \ReflectionClass($fieldFqn);
            $field                    = PhpTrait::fromFile($fieldReflection->getFileName());
            $fieldInterfaceFqn        = \str_replace(
                ['Traits', 'Trait'],
                ['Interfaces', 'Interface'],
                $fieldFqn
            );
            $fieldInterfaceReflection = new \ReflectionClass($fieldInterfaceFqn);
            $fieldInterface           = PhpInterface::fromFile($fieldInterfaceReflection->getFileName());
        } catch (\Exception $e) {
            throw new DoctrineStaticMetaException(
                'Failed loading the entity or field from FQN: '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
        $entity->addTrait($field);
        $entity->addInterface($fieldInterface);
        $this->codeHelper->generate($entity, $entityReflection->getFileName());
    }

    protected function isField(\ReflectionClass $fieldInterfaceReflection)
    {
        $consts = $fieldInterfaceReflection->getConstants();

    }
}
