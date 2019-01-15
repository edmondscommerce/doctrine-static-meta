<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\JsonFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints\DomainName;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints\FieldConstraints\Json;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait JsonFieldTrait
{

    /**
     * @var string|null
     */
    private $json;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForJson(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleStringFields(
            [JsonFieldInterface::PROP_JSON],
            $builder,
            JsonFieldInterface::DEFAULT_JSON,
            false
        );
    }

    /**
     * Use a custom validator to call filter_var to validate that the domain name is valid
     *
     * @param ValidatorClassMetaData $metadata
     */
    protected static function validatorMetaForPropertyJson(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            JsonFieldInterface::PROP_JSON,
            new Json()
        );
    }

    /**
     * @return string|null
     */
    public function getJson(): ?string
    {
        if (null === $this->json) {
            return JsonFieldInterface::DEFAULT_JSON;
        }

        return $this->json;
    }

    /**
     * @param string|null $json
     *
     * @return self
     */
    private function setJson(?string $json): self
    {
        $this->updatePropertyValue(
            JsonFieldInterface::PROP_JSON,
            $json
        );

        return $this;
    }

    private function initJson()
    {
        $this->json = JsonFieldInterface::DEFAULT_JSON;
    }
}
