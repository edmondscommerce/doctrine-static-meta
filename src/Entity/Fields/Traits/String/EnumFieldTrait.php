<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\EnumFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait EnumFieldTrait
{

    /**
     * @var string
     */
    private $enum;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForEnum(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleStringFields(
            [EnumFieldInterface::PROP_ENUM],
            $builder,
            EnumFieldInterface::DEFAULT_ENUM,
            false
        );
    }

    /**
     * This method sets the validation for this field.
     *
     * You should add in as many relevant property constraints as you see fit.
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function validatorMetaForEnum(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            EnumFieldInterface::PROP_ENUM,
            new Choice(EnumFieldInterface::ENUM_OPTIONS)
        );
    }

    private function initEnum(): void
    {
        $this->enum = EnumFieldInterface::DEFAULT_ENUM;
    }

    /**
     * @return string
     */
    public function getEnum(): string
    {
        if (null === $this->enum) {
            return EnumFieldInterface::DEFAULT_ENUM;
        }

        return $this->enum;
    }

    /**
     * Uses the Symfony Validator and fails back to basic in_array validation with exception
     *
     * @param string $enum
     *
     * @return self
     */
    public function setEnum(string $enum): self
    {
        $this->updatePropertyValueThenValidateAndNotify(
            EnumFieldInterface::PROP_ENUM,
            $enum
        );

        return $this;
    }
}
