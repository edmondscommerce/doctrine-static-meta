<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\SettableUuidFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

/**
 * Trait SettableUuidFieldTrait
 *
 * This field allows you to set a UUID that is generated elsewhere than the database.
 * This is as opposed to using a UUID primary key which is generated by the database
 * - eg
 * \EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\PrimaryKey\UuidFieldTrait
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String
 */
trait SettableUuidFieldTrait
{

    /**
     * @var string|null
     */
    private $settableUuid;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForSettableUuid(ClassMetadataBuilder $builder): void
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => SettableUuidFieldInterface::PROP_SETTABLE_UUID,
                'type'      => Type::STRING,
                'default'   => SettableUuidFieldInterface::DEFAULT_SETTABLE_UUID,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(SettableUuidFieldInterface::PROP_SETTABLE_UUID))
            ->nullable(true)
            ->unique(true)
            ->length(100)
            ->build();
    }

    /**
     * This method sets the validation for this field.
     *
     * You should add in as many relevant property constraints as you see fit.
     *
     * @see https://symfony.com/doc/current/validation.html#supported-constraints
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function validatorMetaForSettableUuid(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            SettableUuidFieldInterface::PROP_SETTABLE_UUID,
            new Uuid()
        );
    }

    /**
     * @return string|null
     */
    public function getSettableUuid(): ?string
    {
        if (null === $this->settableUuid) {
            return SettableUuidFieldInterface::DEFAULT_SETTABLE_UUID;
        }

        return $this->settableUuid;
    }

    /**
     * @param string|null $settableUuid
     *
     * @return self
     */
    private function setSettableUuid(?string $settableUuid): self
    {
        $this->updatePropertyValueThenValidateAndNotify(
            SettableUuidFieldInterface::PROP_SETTABLE_UUID,
            $settableUuid
        );

        return $this;
    }
}
