<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean\DefaultsNullFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait DefaultsNullFieldTrait
{

    /**
     * @var bool|null
     */
    private $defaultsNull;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForDefaultsNull(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleBooleanFields(
            [DefaultsNullFieldInterface::PROP_DEFAULTS_NULL],
            $builder,
            DefaultsNullFieldInterface::DEFAULT_DEFAULTS_NULL
        );
    }

    /**
     * @return bool|null
     */
    public function isDefaultsNull(): ?bool
    {
        return $this->defaultsNull;
    }

    /**
     * @param bool|null $defaultsNull
     *
     * @return self
     */
    private function setDefaultsNull(?bool $defaultsNull): self
    {
        $this->updatePropertyValueThenValidateAndNotify(
            DefaultsNullFieldInterface::PROP_DEFAULTS_NULL,
            $defaultsNull
        );

        return $this;
    }
}
