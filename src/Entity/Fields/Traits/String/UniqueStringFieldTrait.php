<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UniqueStringFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait UniqueStringFieldTrait
{

    /**
     * @var string|null
     */
    private $uniqueString;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForUniqueString(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleStringFields(
            [UniqueStringFieldInterface::PROP_UNIQUE_STRING],
            $builder,
            UniqueStringFieldInterface::DEFAULT_UNIQUE_STRING,
            true
        );
    }

    /**
     * This method sets the validation for this field.
     *
     * You should add in as many relevant property constraints as you see fit.
     *
     * Remove the PHPMD suppressed warning once you start setting constraints
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @see https://symfony.com/doc/current/validation.html#supported-constraints
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function validatorMetaForUniqueString(ValidatorClassMetaData $metadata)
    {
        //        $metadata->addPropertyConstraint(
        //            UniqueStringFieldInterface::PROP_UNIQUE_STRING,
        //            new NotBlank()
        //        );
    }

    /**
     * @return string|null
     */
    public function getUniqueString(): ?string
    {
        if (null === $this->uniqueString) {
            return UniqueStringFieldInterface::DEFAULT_UNIQUE_STRING;
        }

        return $this->uniqueString;
    }

    /**
     * @param string|null $uniqueString
     *
     * @return self
     */
    public function setUniqueString(?string $uniqueString): self
    {
        $this->uniqueString = $uniqueString;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(UniqueStringFieldInterface::PROP_UNIQUE_STRING);
        }

        return $this;
    }
}
