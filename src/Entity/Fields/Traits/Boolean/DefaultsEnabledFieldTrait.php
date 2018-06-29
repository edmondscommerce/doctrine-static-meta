<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean\DefaultsEnabledFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

trait DefaultsEnabledFieldTrait
{

    /**
     * @var bool
     */
    private $defaultsEnabled = DefaultsEnabledFieldInterface::DEFAULT_DEFAULTS_ENABLED;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForDefaultsEnabled(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleBooleanFields(
            [DefaultsEnabledFieldInterface::PROP_DEFAULTS_ENABLED],
            $builder,
            DefaultsEnabledFieldInterface::DEFAULT_DEFAULTS_ENABLED
        );
    }

    private function initDefaultsEnabled(): void
    {
        $this->defaultsEnabled = DefaultsEnabledFieldInterface::DEFAULT_DEFAULTS_ENABLED;
    }

    /**
     * @return bool
     */
    public function isDefaultsEnabled(): bool
    {
        if (null === $this->defaultsEnabled) {
            return DefaultsEnabledFieldInterface::DEFAULT_DEFAULTS_ENABLED;
        }

        return $this->defaultsEnabled;
    }

    /**
     * @param bool|null $defaultsEnabled
     *
     * @return self
     */
    public function setDefaultsEnabled(bool $defaultsEnabled): self
    {
        $this->updatePropertyValueAndNotify(DefaultsEnabledFieldInterface::PROP_DEFAULTS_ENABLED, $defaultsEnabled);

        return $this;
    }
}
