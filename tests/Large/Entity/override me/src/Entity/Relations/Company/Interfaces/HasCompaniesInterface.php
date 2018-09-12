<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Company\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\CompanyInterface;

interface HasCompaniesInterface
{
    public const PROPERTY_NAME_COMPANIES = 'companies';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForCompanies(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|CompanyInterface[]
     */
    public function getCompanies(): Collection;

    /**
     * @param Collection|CompanyInterface[] $companies
     *
     * @return self
     */
    public function setCompanies(Collection $companies): self;

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
    ): HasCompaniesInterface;

    /**
     * @param CompanyInterface $company
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCompany(
        CompanyInterface $company,
        bool $recip = true
    ): HasCompaniesInterface;

}
