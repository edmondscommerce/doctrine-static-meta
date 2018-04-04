<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag;

// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Flag\IsDefaultFieldInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait IsDefaultFieldTrait
{

    /**
     * @var int
     */
    private $isDefault;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForIsDefault(ClassMetadataBuilder $builder)
    {
        MappingHelper::setSimpleIntegerFields(
            [IsDefaultFieldInterface::PROP_IS_DEFAULT],
            $builder,
            false
        );
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function getPropertyValidatorMetaForIsDefault(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            IsDefaultFieldInterface::PROP_IS_DEFAULT,
            new Range(0, 1)
        );

        $metadata->addPropertyConstraint(
            IsDefaultFieldInterface::PROP_IS_DEFAULT,
            new NotNull()
        );
    }

    /**
     * @return int
     */
    public function getIsDefault(): int
    {
        return $this->isDefault;
    }

    /**
     * @param int $isDefault
     * @return $this|IsDefaultFieldInterface
     */
    public function setIsDefault(int $isDefault)
    {
        $this->isDefault = $isDefault;
        return $this;
    }
}
