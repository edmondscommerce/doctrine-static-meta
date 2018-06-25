<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\BusinessIdentifierCodeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Bic;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable

/**
 * Trait BusinessIdentifierCodeFieldTrait
 *
 * A Business Identifier Code also known as SWIFT-BIC, BIC, SWIFT ID or SWIFT code
 *
 * @see     https://en.wikipedia.org/wiki/ISO_9362
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String
 */
trait BusinessIdentifierCodeFieldTrait
{

    /**
     * @var string|null
     */
    private $businessIdentifierCode;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForBusinessIdentifierCode(ClassMetadataBuilder $builder)
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => BusinessIdentifierCodeFieldInterface::PROP_BUSINESS_IDENTIFIER_CODE,
                'type'      => Type::STRING,
                'default'   => BusinessIdentifierCodeFieldInterface::DEFAULT_BUSINESS_IDENTIFIER_CODE,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(
                BusinessIdentifierCodeFieldInterface::PROP_BUSINESS_IDENTIFIER_CODE
            ))
            ->nullable(BusinessIdentifierCodeFieldInterface::DEFAULT_BUSINESS_IDENTIFIER_CODE === null)
            ->unique(false)
            ->length(20)
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
    protected static function validatorMetaForBusinessIdentifierCode(ValidatorClassMetaData $metadata)
    {
        $metadata->addPropertyConstraint(
            BusinessIdentifierCodeFieldInterface::PROP_BUSINESS_IDENTIFIER_CODE,
            new Bic()
        );
    }

    /**
     * @return string|null
     */
    public function getBusinessIdentifierCode(): ?string
    {
        if (null === $this->businessIdentifierCode) {
            return BusinessIdentifierCodeFieldInterface::DEFAULT_BUSINESS_IDENTIFIER_CODE;
        }

        return $this->businessIdentifierCode;
    }

    /**
     * @param string|null $businessIdentifierCode
     *
     * @return self
     */
    public function setBusinessIdentifierCode(?string $businessIdentifierCode): self
    {
        $this->businessIdentifierCode = $businessIdentifierCode;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(BusinessIdentifierCodeFieldInterface::PROP_BUSINESS_IDENTIFIER_CODE);
        }

        return $this;
    }
}
