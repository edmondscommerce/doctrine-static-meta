<?php declare(strict_types=1);
/**
 * @category EdmondsCommerce
 * @package  EdmondsCommerce_
 * @author   Ross Mitchell <ross@edmondscommerce.co.uk>
 */

namespace EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\Field;

use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\CodeHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\Generator\FindAndReplaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\CodeGeneration\NamespaceHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\PrimaryKey\SettableIdFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\IdFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\IntegerIdFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\NonBinaryUuidFieldTrait;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait;
use gossi\codegen\model\PhpInterface;
use ts\Reflection\ReflectionClass;

class IdTraitSetter
{
    public const ID_FIELD_TRAIT         = IdFieldTrait::class;
    public const INTEGER_ID_FIELD_TRAIT = IntegerIdFieldTrait::class;
    public const NON_BINARY_UUID_TRAIT  = NonBinaryUuidFieldTrait::class;
    public const UUID_FIELD_TRAIT       = UuidFieldTrait::class;

    private const SET_ID_METHOD = 'setId';
    /**
     * @var FindAndReplaceHelper
     */
    protected $findAndReplaceHelper;
    /**
     * @var NamespaceHelper
     */
    protected $namespaceHelper;
    /**
     * @var CodeHelper
     */
    protected $codeHelper;

    private $idTraitToUse = self::UUID_FIELD_TRAIT;

    /**
     * @var ReflectionClass
     */
    private $idTraitReflection;

    public function __construct(
        FindAndReplaceHelper $findAndReplaceHelper,
        NamespaceHelper $namespaceHelper,
        CodeHelper $codeHelper
    ) {
        $this->findAndReplaceHelper = $findAndReplaceHelper;
        $this->namespaceHelper      = $namespaceHelper;
        $this->codeHelper           = $codeHelper;
    }

    public function setIdTrait(string $type): void
    {
        switch ($type) {
            case self::ID_FIELD_TRAIT:
            case self::INTEGER_ID_FIELD_TRAIT:
            case self::NON_BINARY_UUID_TRAIT:
            case self::UUID_FIELD_TRAIT:
                $this->idTraitToUse      = $type;
                $this->idTraitReflection = new ReflectionClass($type);
                break;
            default:
                throw new \LogicException("Unknown ID trait of $type given");
        }
    }

    public function updateEntity(string $entityFqn): void
    {
        $filePath = (new ReflectionClass($entityFqn))->getFileName();
        if ($this->idTraitToUse === self::ID_FIELD_TRAIT) {
            return;
        }
        $useStatement = $this->getUseStatement();
        $this->findAndReplaceHelper->findReplace(
            'use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;',
            $useStatement,
            $filePath
        );
        $this->extendEntityInterfaceIfSettable($entityFqn);
    }

    private function getUseStatement()
    {
        switch ($this->idTraitToUse) {
            case self::ID_FIELD_TRAIT:
                $useStatement = 'use DSM\Fields\Traits\PrimaryKey\IdFieldTrait;';
                break;
            case self::INTEGER_ID_FIELD_TRAIT:
                $useStatement = 'use DSM\Fields\Traits\PrimaryKey\IntegerIdFieldTrait;';
                break;
            case self::NON_BINARY_UUID_TRAIT:
                $useStatement = 'use DSM\Fields\Traits\PrimaryKey\NonBinaryUuidFieldTrait;';
                break;
            case self::UUID_FIELD_TRAIT:
                $useStatement = 'use DSM\Fields\Traits\PrimaryKey\UuidFieldTrait;';
                break;
            default:
                throw new \LogicException('Unknown trait selected');
        }

        return $useStatement;
    }

    /**
     * Check if the chosen trait implements the `setId` method
     *
     * If it does, then we should add the relevant interface to the EntityInterface
     *
     * @param string $entityFqn
     *
     * @throws \ReflectionException
     */
    private function extendEntityInterfaceIfSettable(string $entityFqn)
    {
        if ($this->idTraitReflection->hasMethod(self::SET_ID_METHOD)) {
            $interfaceFqn            = $this->namespaceHelper->getEntityInterfaceFromEntityFqn($entityFqn);
            $interfacePath           = (new ReflectionClass($interfaceFqn))->getFileName();
            $interface               = PhpInterface::fromFile($interfacePath);
            $settableIdInterfacePath = (new ReflectionClass(SettableIdFieldInterface::class))->getFileName();
            $interface->addInterface(PhpInterface::fromFile($settableIdInterfacePath));
            $this->codeHelper->generate($interface, $interfacePath);
        }
    }
}
