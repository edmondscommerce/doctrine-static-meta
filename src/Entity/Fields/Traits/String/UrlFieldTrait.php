<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\UrlFieldInterface;
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
     * This method validates that the url is valid
     *
     * It allows the protocol to be ommitted, eg //www.edmondscommerce.co.uk
     *
     * You can extend the list of allowed protocols as you see fit
     */
    protected static function validatorMetaForUrl(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            UrlFieldInterface::PROP_URL,
            new Url([
                        'relativeProtocol' => true,
                        'protocols'        => ['http', 'https'],
                    ])
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
    private function setUrl(?string $url): self
    {
        $this->updatePropertyValueThenValidateAndNotify(
            UrlFieldInterface::PROP_URL,
            $url
        );

        return $this;
    }
}
