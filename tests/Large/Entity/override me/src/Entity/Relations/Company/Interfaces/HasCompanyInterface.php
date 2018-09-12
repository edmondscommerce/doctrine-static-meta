<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Company\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\CompanyInterface;

interface HasCompanyInterface
{
    public const PROPERTY_NAME_COMPANY = 'company';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForCompany(ClassMetadataBuilder $builder): void;

    /**
     * @return null|CompanyInterface
     */
    public function getCompany(): ?CompanyInterface;

    /**
     * @param CompanyInterface|null $company
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setCompany(
        ?CompanyInterface $company,
        bool $recip = true
    ): HasCompanyInterface;

    /**
     * @param null|CompanyInterface $company
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCompany(
        ?CompanyInterface $company = null,
        bool $recip = true
    ): HasCompanyInterface;
}
