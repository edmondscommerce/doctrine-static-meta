<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\String;

// phpcs:disable Generic.Files.LineLength.TooLong

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\String\JsonDataFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints\DomainName;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Validation\Constraints\FieldConstraints\JsonData;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait JsonDataFieldTrait
{

    /**
     * @var string|null
     */
    private $jsonData;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForJson(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleStringFields(
            [JsonDataFieldInterface::PROP_JSON_DATA],
            $builder,
            JsonDataFieldInterface::DEFAULT_JSON_DATA,
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
            JsonDataFieldInterface::PROP_JSON_DATA,
            new JsonData()
        );
    }

    /**
     * @return string|null
     */
    public function getJsonData(): ?string
    {
        if (null === $this->jsonData) {
            return JsonDataFieldInterface::DEFAULT_JSON_DATA;
        }

        return $this->jsonData;
    }

    /**
     * @param string|null $json
     *
     * @return self
     */
    private function setJsonData(?string $json): self
    {
        $this->updatePropertyValue(
            JsonDataFieldInterface::PROP_JSON_DATA,
            $json
        );

        return $this;
    }

    private function initJsonData()
    {
        $this->jsonData = JsonDataFieldInterface::DEFAULT_JSON_DATA;
    }
}
