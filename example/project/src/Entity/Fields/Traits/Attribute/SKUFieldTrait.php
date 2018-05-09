<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Traits\Attribute;

// phpcs:disable

use \Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use \EdmondsCommerce\DoctrineStaticMeta\MappingHelper;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\ValidatedEntityInterface;
use My\Test\Project\Entity\Fields\Interfaces\Attribute\SKUFieldInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;

// phpcs:enable
trait SKUFieldTrait
{

    /**
     * @var string
     */
    private $sKU;

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForSKU(ClassMetadataBuilder $builder)
    {
        MappingHelper::setSimpleStringFields(
            [SKUFieldInterface::PROP_S_K_U],
            $builder,
            false
        );
    }

    /**
     * This method sets the validation for this field.
     *
     * You should add in as many relevant property constraints as you see fit.
     *
     * Remove the PHPMD suppressed warning once you start setting constraints
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ValidatorClassMetaData $metadata
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    protected static function getPropertyValidatorMetaForSKU(ValidatorClassMetaData $metadata)
    {
        //        $metadata->addPropertyConstraint(
        //            SKUFieldInterface::PROP_S_K_U,
        //            new NotBlank()
        //        );
    }

    /**
     * @return string
     */
    public function getSKU(): string
    {
        return $this->sKU;
    }

    /**
     * @param string $sKU
     * @return self
     */
    public function setSKU(string $sKU): self
    {
        $this->sKU = $sKU;
        if ($this instanceof ValidatedEntityInterface) {
            $this->validateProperty(SKUFieldInterface::PROP_S_K_U);
        }
        return $this;
    }
}
