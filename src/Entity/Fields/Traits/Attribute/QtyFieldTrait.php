<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute\QtyFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait QtyFieldTrait
{

    /**
     * @var int|null
     */
    private $qty;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForQty(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleIntegerFields(
            [QtyFieldInterface::PROP_QTY],
            $builder,
            true
        );
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function validatorMetaForQty(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            QtyFieldInterface::PROP_QTY,
            new GreaterThanOrEqual(0)
        );
    }

    /**
     * @return int|null
     */
    public function getQty(): ?int
    {
        return $this->qty;
    }

    /**
     * @param int|null $qty
     *
     * @return $this|QtyFieldInterface
     */
    public function setQty(?int $qty): self
    {
        $this->qty = $qty;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(QtyFieldInterface::PROP_QTY);
        }

        return $this;
    }
}
