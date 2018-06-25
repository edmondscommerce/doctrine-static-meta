<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
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
            $this->checkInterfaceLooksLikeField($fieldInterfaceReflection);
            $fieldInterface = PhpInterface::fromFile($fieldInterfaceReflection->getFileName());
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

    /**
     * @param \ReflectionClass $fieldInterfaceReflection
     */
    protected function checkInterfaceLooksLikeField(\ReflectionClass $fieldInterfaceReflection): void
    {
        $notFound = [
            'PROP_',
            'DEFAULT_',
        ];
        $consts   = $fieldInterfaceReflection->getConstants();
        foreach (\array_keys($consts) as $name) {
            foreach ($notFound as $key => $prefix) {
                if (0 === strpos($name, $prefix)) {
                    unset($notFound[$key]);
                }
            }
        }
        if ([] !== $notFound) {
            throw new \InvalidArgumentException(
                'Field '.$fieldInterfaceReflection->getName()
                .' does not look like a field interface, failed to find the following const prefixes: '
                ."\n".print_r($notFound, true)
            );
        }
    }
}
