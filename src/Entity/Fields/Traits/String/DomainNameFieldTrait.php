<?php

declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\DomainNameFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints\FieldConstraints\DomainName;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Schema\Database;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait DomainNameFieldTrait
{

    /**
     * @var string|null
     */
    private $domainName;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForDomainName(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleStringFields(
            [DomainNameFieldInterface::PROP_DOMAIN_NAME],
            $builder,
            DomainNameFieldInterface::DEFAULT_DOMAIN_NAME
        );
    }

    /**
     * Use a custom validator to call filter_var to validate that the domain name is valid
     *
     * @param ValidatorClassMetaData $metadata
     */
    protected static function validatorMetaForPropertyDomainName(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraints(
            DomainNameFieldInterface::PROP_DOMAIN_NAME,
            [
                new DomainName(),
                new Length(
                    [
                        'min' => 0,
                        'max' => Database::MAX_VARCHAR_LENGTH,
                    ]
                ),
            ]
        );
    }

    /**
     * @return string|null
     */
    public function getDomainName(): ?string
    {
        if (null === $this->domainName) {
            return DomainNameFieldInterface::DEFAULT_DOMAIN_NAME;
        }

        return $this->domainName;
    }

    /**
     * @param string|null $domainName
     *
     * @return self
     */
    private function setDomainName(?string $domainName): self
    {
        $this->updatePropertyValue(
            DomainNameFieldInterface::PROP_DOMAIN_NAME,
            $domainName
        );

        return $this;
    }

    private function initDomainName(): void
    {
        $this->domainName = DomainNameFieldInterface::DEFAULT_DOMAIN_NAME;
    }
}
