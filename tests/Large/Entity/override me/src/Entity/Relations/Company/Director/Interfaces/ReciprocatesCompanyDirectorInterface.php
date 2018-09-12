<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Company\Director\Interfaces;

use My\Test\Project\Entity\Interfaces\Company\DirectorInterface;

interface ReciprocatesCompanyDirectorInterface
{
    /**
     * @param DirectorInterface $companyDirector
     *
     * @return self
     */
    public function reciprocateRelationOnCompanyDirector(
        DirectorInterface $companyDirector
    ): self;

    /**
     * @param DirectorInterface $companyDirector
     *
     * @return self
     */
    public function removeRelationOnCompanyDirector(
        DirectorInterface $companyDirector
    ): self;
}
