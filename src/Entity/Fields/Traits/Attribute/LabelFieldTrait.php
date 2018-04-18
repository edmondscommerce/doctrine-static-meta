<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Attribute;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Attribute\LabelFieldInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

trait LabelFieldTrait
{
    /**
     * @var string
     */
    private $label;

    protected static function getPropertyDoctrineMetaForLabel(ClassMetadataBuilder $builder): void
    {
        $builder->createField(LabelFieldInterface::PROP_LABEL, Type::STRING)
                ->nullable(true)
                ->length(255)
                ->build();
    }

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function getPropertyValidatorMetaForLabel(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraints(
            LabelFieldInterface::PROP_LABEL,
            [
                new Length([
                               'min' => 2,
                               'max' => 255,
                           ]),
            ]
        );
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(?string $label): self
    {
        $this->label = $label;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(LabelFieldInterface::PROP_LABEL);
        }

        return $this;
    }
}
