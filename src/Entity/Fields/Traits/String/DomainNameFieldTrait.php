<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\DomainNameFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
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
            DomainNameFieldInterface::DEFAULT_DOMAIN_NAME,
            false
        );
    }

    /**
     * Use a Callback validator to call filter_var to validate that the domain name is valid
     */
    protected static function validatorMetaForDomainName(ValidatorClassMetaData $metadata)
    {
        $metadata->addPropertyConstraint(
            DomainNameFieldInterface::PROP_DOMAIN_NAME,
            new Callback(function ($domainName, ExecutionContextInterface $context) {
                if (false === filter_var($domainName, FILTER_VALIDATE_DOMAIN)
                    || false === \ts\stringContains($domainName, '.')
                    || false !== \ts\stringContains($domainName, '//')
                ) {
                    $context->addViolation('The domain name "' . $domainName . '" is not valid.');
                }
            })
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
    public function setDomainName(?string $domainName): self
    {
        $this->updatePropertyValueThenValidateAndNotify(
            DomainNameFieldInterface::PROP_DOMAIN_NAME,
            $domainName
        );

        return $this;
    }

    private function initDomainName()
    {
        $this->domainName = DomainNameFieldInterface::DEFAULT_DOMAIN_NAME;
    }
}
