<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Company\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata as ValidatorClassMetaData;
use My\Test\Project\Entity\Interfaces\CompanyInterface;
use My\Test\Project\Entity\Relations\Company\Interfaces\HasCompaniesInterface;
use My\Test\Project\Entity\Relations\Company\Interfaces\ReciprocatesCompanyInterface;

/**
 * Trait HasCompaniesAbstract
 *
 * The base trait for relations to multiple Companies
 *
 * @package Test\Code\Generator\Entity\Relations\Company\Traits
 */
// phpcs:enable
trait HasCompaniesAbstract
{
    /**
     * @var ArrayCollection|CompanyInterface[]
     */
    private $companies;

    /**
     * @param ValidatorClassMetaData $metadata
     *
     * @throws \Symfony\Component\Validator\Exception\MissingOptionsException
     * @throws \Symfony\Component\Validator\Exception\InvalidOptionsException
     * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public static function validatorMetaForCompanies(
        ValidatorClassMetaData $metadata
    ): void {
        $metadata->addPropertyConstraint(
            HasCompaniesInterface::PROPERTY_NAME_COMPANIES,
            new Valid()
        );
    }

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function metaForCompanies(
        ClassMetadataBuilder $manyToManyBuilder
    ): void;

    /**
     * @return Collection|CompanyInterface[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    /**
     * @param Collection|CompanyInterface[] $companies
     *
     * @return self
     */
    public function setCompanies(
        Collection $companies
    ): HasCompaniesInterface {
        $this->setEntityCollectionAndNotify(
            'companies',
            $companies
        );

        return $this;
    }

    /**
     * @param CompanyInterface|null $company
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addCompany(
        ?CompanyInterface $company,
        bool $recip = true
    ): HasCompaniesInterface {
        if ($company === null) {
            return $this;
        }

        $this->addToEntityCollectionAndNotify('companies', $company);
        if ($this instanceof ReciprocatesCompanyInterface && true === $recip) {
            $this->reciprocateRelationOnCompany(
                $company
            );
        }

        return $this;
    }

    /**
     * @param CompanyInterface $company
     * @param bool                    $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCompany(
        CompanyInterface $company,
        bool $recip = true
    ): HasCompaniesInterface {
        $this->removeFromEntityCollectionAndNotify('companies', $company);
        if ($this instanceof ReciprocatesCompanyInterface && true === $recip) {
            $this->removeRelationOnCompany(
                $company
            );
        }

        return $this;
    }

    /**
     * Initialise the companies property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initCompanies()
    {
        $this->companies = new ArrayCollection();

        return $this;
    }
}
