<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\LabelFieldInterface;
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
        $builder->createField(LabelFieldInterface::PROPERTY_NAME, Type::STRING)
                ->nullable(false)
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
            LabelFieldInterface::PROPERTY_NAME,
            [
                new Length([
                               'min' => 2,
                           ]),
            ]
        );
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel(): string
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
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }
}
