<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Flag;

// phpcs:disable

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\Builder\FieldBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Flag\DefaultFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
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
     * @param ClassMetadataBuilder $builder
     */
    public static function metaForIsDefault(ClassMetadataBuilder $builder): void
    {
        $fieldBuilder = new FieldBuilder($builder, [
            'default'   => DefaultFieldInterface::DEFAULT_DEFAULT,
            'fieldName' => '`'.DefaultFieldInterface::PROP_DEFAULT.'`',
            'type'      => MappingHelper::TYPE_BOOLEAN,
            'nullable'  => false,
        ]);
        $fieldBuilder->build();
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function validatorMetaForIsDefault(ValidatorClassMetaData $metadata): void
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
        if (null === $this->default) {
            return DefaultFieldInterface::DEFAULT_DEFAULT;
        }

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
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(DefaultFieldInterface::PROP_DEFAULT);
        }

        return $this;
    }
}
