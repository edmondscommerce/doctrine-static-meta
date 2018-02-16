<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits\Fields;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\Fields\NameFieldInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;


trait NameFieldTrait
{
    /**
     * @var string
     */
    private $name;

    protected static function getPropertyDoctrineMetaForName(ClassMetadataBuilder $builder): void
    {
        $builder->createField('name', Type::STRING)
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
    protected static function getPropertyValidatorMetaForName(ValidatorClassMetaData $metadata): void
    {
        $metadata->addPropertyConstraint(
            NameFieldInterface::PROPERTY_NAME,
            new Length(['min' => 2])
        );
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }
}
