<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UrlFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait UrlFieldTrait
{

    /**
     * @var string|null
     */
    private $url;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForUrl(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleStringFields(
            [UrlFieldInterface::PROP_URL],
            $builder,
            UrlFieldInterface::DEFAULT_URL,
            false
        );
    }

    /**
     * This method sets the validation for this field.
     *
     * You should add in as many relevant property constraints as you see fit.
     *
     * Remove the PHPMD suppressed warning once you start setting constraints
     *
     * @see https://symfony.com/doc/current/validation.html#supported-constraints
     *
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function validatorMetaForUrl(ValidatorClassMetaData $metadata)
    {
        $metadata->addPropertyConstraint(
            UrlFieldInterface::PROP_URL,
            new Url()
        );
    }

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        if (null === $this->url) {
            return UrlFieldInterface::DEFAULT_URL;
        }

        return $this->url;
    }

    /**
     * @param string|null $url
     *
     * @return self
     */
    public function setUrl(?string $url): self
    {
        $this->updatePropertyValueThenValidateAndNotify(
            UrlFieldInterface::PROP_URL,
            $url
        );

        return $this;
    }
}
