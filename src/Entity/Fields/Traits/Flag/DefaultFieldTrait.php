<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Flag\DefaultFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait DefaultFieldTrait
{

    /**
     * @var bool
     */
    private $default;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForIsDefault(ClassMetadataBuilder $builder)
    {
        MappingHelper::setSimpleBooleanFields(
            [DefaultFieldInterface::PROP_DEFAULT],
            $builder,
            false
        );
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function getPropertyValidatorMetaForIsDefault(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            DefaultFieldInterface::PROP_DEFAULT,
            new NotNull()
        );
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }

    /**
     * @param bool $default
     *
     * @return $this|DefaultFieldInterface
     */
    public function setDefault(bool $default): self
    {
        $this->default = $default;
        if ($this instanceof EntityInterface) {
            $this->validateProperty(DefaultFieldInterface::PROP_DEFAULT);
        }

        return $this;
    }
}
