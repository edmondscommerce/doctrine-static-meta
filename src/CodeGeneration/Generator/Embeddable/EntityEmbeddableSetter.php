<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Embeddable;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpInterface;
use gossi\codegen\model\PhpTrait;

class EntityEmbeddableSetter
{
    /**
     * @var CodeHelper
     */
    protected $codeHelper;

    public function __construct(CodeHelper $codeHelper)
    {
        $this->codeHelper = $codeHelper;
    }

    public function setEntityHasEmbeddable(string $entityFqn, string $embeddableTraitFqn)
    {
        $entityReflection     = new \ReflectionClass($entityFqn);
        $entity               = PhpClass::fromFile($entityReflection->getFileName());
        $embeddableReflection = new \ReflectionClass($embeddableTraitFqn);
        $trait                = PhpTrait::fromFile($embeddableReflection->getFileName());
        $interfaceFqn         = \str_replace(
            'Trait',
            'Interface',
            $embeddableTraitFqn
        );
        $interfaceReflection  = new \ReflectionClass($interfaceFqn);
        $interface            = PhpInterface::fromFile($interfaceReflection->getFileName());
        $entity->addTrait($trait)
               ->addInterface($interface);
        $this->codeHelper->generate($entity, $entityReflection->getFileName());
    }
}
