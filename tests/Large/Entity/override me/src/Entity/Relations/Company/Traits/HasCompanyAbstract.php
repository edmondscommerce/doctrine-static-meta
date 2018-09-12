<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Company\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\EntityInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entities\Company as Company;
use My\Test\Project\Entity\Interfaces\CompanyInterface;
use My\Test\Project\Entity\Relations\Company\Interfaces\HasCompanyInterface;
use My\Test\Project\Entity\Relations\Company\Interfaces\ReciprocatesCompanyInterface;

/**
 * Trait HasCompanyAbstract
 *
 * The base trait for relations to a single Company
 *
 * @package Test\Code\Generator\Entity\Relations\Company\Traits
 */
// phpcs:enable
trait HasCompanyAbstract
{
    /**
     * @var Company|null
     */
    private $company;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function metaForCompany(
        ClassMetadataBuilder $builder
    ): void;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForCompany(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasCompanyInterface::PROPERTY_NAME_COMPANY,
            new Valid()
        );
    }

    /**
     * @param null|CompanyInterface $company
     * @param bool                         $recip
     *
     * @return HasCompanyInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCompany(
        ?CompanyInterface $company = null,
        bool $recip = true
    ): HasCompanyInterface {
        if (
            $this instanceof ReciprocatesCompanyInterface
            && true === $recip
        ) {
            if (!$company instanceof EntityInterface) {
                $company = $this->getCompany();
            }
            $remover = 'remove' . self::getDoctrineStaticMeta()->getSingular();
            $company->$remover($this, false);
        }

        return $this->setCompany(null, false);
    }

    /**
     * @return CompanyInterface|null
     */
    public function getCompany(): ?CompanyInterface
    {
        return $this->company;
    }

    /**
     * @param CompanyInterface|null $company
     * @param bool                         $recip
     *
     * @return HasCompanyInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setCompany(
        ?CompanyInterface $company,
        bool $recip = true
    ): HasCompanyInterface {

        $this->setEntityAndNotify('company', $company);
        if (
            $this instanceof ReciprocatesCompanyInterface
            && true === $recip
            && null !== $company
        ) {
            $this->reciprocateRelationOnCompany($company);
        }

        return $this;
    }
}
