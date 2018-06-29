<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\CountryCodeFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

trait CountryCodeFieldTrait
{

    /**
     * @var string|null
     */
    private $countryCode;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForCountryCode(ClassMetadataBuilder $builder)
    {
        $fieldBuilder = new FieldBuilder(
            $builder,
            [
                'fieldName' => CountryCodeFieldInterface::PROP_COUNTRY_CODE,
                'type'      => Type::STRING,
                'default'   => CountryCodeFieldInterface::DEFAULT_COUNTRY_CODE,
            ]
        );
        $fieldBuilder
            ->columnName(MappingHelper::getColumnNameForField(CountryCodeFieldInterface::PROP_COUNTRY_CODE))
            ->nullable(CountryCodeFieldInterface::DEFAULT_COUNTRY_CODE === null)
            ->unique(false)
            ->length(6)
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
    protected static function validatorMetaForCountryCode(ValidatorClassMetaData $metadata)
    {
        $metadata->addPropertyConstraint(
            CountryCodeFieldInterface::PROP_COUNTRY_CODE,
            new Country()
        );
    }

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        if (null === $this->countryCode) {
            return CountryCodeFieldInterface::DEFAULT_COUNTRY_CODE;
        }

        return $this->countryCode;
    }

    /**
     * @param string|null $countryCode
     *
     * @return self
     */
    public function setCountryCode(?string $countryCode): self
    {
        $this->updatePropertyValueAndNotify(
            CountryCodeFieldInterface::PROP_COUNTRY_CODE,
            $countryCode
        );
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(CountryCodeFieldInterface::PROP_COUNTRY_CODE);
        }

        return $this;
    }
}
