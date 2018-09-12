<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Company\Director\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Interfaces\Company\DirectorInterface;

interface HasCompanyDirectorInterface
{
    public const PROPERTY_NAME_COMPANY_DIRECTOR = 'companyDirector';

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function metaForCompanyDirector(ClassMetadataBuilder $builder): void;

    /**
     * @return null|DirectorInterface
     */
    public function getCompanyDirector(): ?DirectorInterface;

    /**
     * @param DirectorInterface|null $companyDirector
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setCompanyDirector(
        ?DirectorInterface $companyDirector,
        bool $recip = true
    ): HasCompanyDirectorInterface;

    /**
     * @param null|DirectorInterface $companyDirector
     * @param bool                         $recip
     *
     * @return self
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeCompanyDirector(
        ?DirectorInterface $companyDirector = null,
        bool $recip = true
    ): HasCompanyDirectorInterface;
}
