<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Company\Director\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Company\DirectorInterface;

interface HasCompanyDirectorsInterface
{
    public const PROPERTY_NAME_COMPANY_DIRECTORS = 'companyDirectors';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForCompanyDirectors(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|DirectorInterface[]
     */
    public function getCompanyDirectors(): Collection;

    /**
     * @param Collection|DirectorInterface[] $companyDirectors
     *
     * @return self
     */
    public function setCompanyDirectors(Collection $companyDirectors): self;

    /**
     * @param DirectorInterface|null $companyDirector
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addCompanyDirector(
        ?DirectorInterface $companyDirector,
        bool $recip = true
    ): HasCompanyDirectorsInterface;

    /**
     * @param DirectorInterface $companyDirector
     * @param bool           $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCompanyDirector(
        DirectorInterface $companyDirector,
        bool $recip = true
    ): HasCompanyDirectorsInterface;

}
