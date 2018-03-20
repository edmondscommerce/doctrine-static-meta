<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Traits\Person;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Person\NameFieldInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

trait NameFieldTrait
{
    /**
     * @var string
     */
    private $name;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected static function getPropertyDoctrineMetaForName(ClassMetadataBuilder $builder): void
    {
        MappingHelper::setSimpleStringFields(
            [NameFieldInterface::PROP_NAME],
            $builder
        );
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
            NameFieldInterface::PROP_NAME,
            new Length(['min' => 2])
        );
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): ?string
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
    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }
}
