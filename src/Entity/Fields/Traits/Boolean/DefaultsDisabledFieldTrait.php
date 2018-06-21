<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Boolean;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Boolean\DefaultsDisabledFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;

trait DefaultsDisabledFieldTrait
{

    /**
     * @var bool
     */
    private $defaultsDisabled = false;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForDefaultsDisabled(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleBooleanFields(
            [DefaultsDisabledFieldInterface::PROP_DEFAULTS_DISABLED],
            $builder,
            DefaultsDisabledFieldInterface::DEFAULT_DEFAULTS_DISABLED
        );
    }

    /**
     * @return bool
     */
    public function isDefaultsDisabled(): bool
    {
        return $this->defaultsDisabled;
    }

    /**
     * @param bool $defaultsDisabled
     *
     * @return self
     */
    public function setDefaultsDisabled(bool $defaultsDisabled): self
    {
        $this->defaultsDisabled = $defaultsDisabled;

        return $this;
    }
}
