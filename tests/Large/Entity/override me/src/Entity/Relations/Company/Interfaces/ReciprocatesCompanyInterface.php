<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Company\Interfaces;

use My\Test\Project\Entity\Interfaces\CompanyInterface;

interface ReciprocatesCompanyInterface
{
    /**
     * @param CompanyInterface $company
     *
     * @return self
     */
    public function reciprocateRelationOnCompany(
        CompanyInterface $company
    ): self;

    /**
     * @param CompanyInterface $company
     *
     * @return self
     */
    public function removeRelationOnCompany(
        CompanyInterface $company
    ): self;
}
