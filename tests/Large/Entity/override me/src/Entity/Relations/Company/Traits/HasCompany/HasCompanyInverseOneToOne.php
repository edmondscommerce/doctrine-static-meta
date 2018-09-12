<?php declare(strict_types=1);
// phpcs:disable

namespace My\Test\Project\Entity\Relations\Company\Traits\HasCompany;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Company as Company;
use My\Test\Project\Entity\Relations\Company\Traits\HasCompanyAbstract;
use My\Test\Project\Entity\Relations\Company\Traits\ReciprocatesCompany;

/**
 * Trait HasCompanyInverseOneToOne
 *
 * The inverse side of a One to One relationship between the Current Entity
 * and Company
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\Company\Traits\HasCompany
 */
// phpcs:enable
trait HasCompanyInverseOneToOne
{
    use HasCompanyAbstract;

    use ReciprocatesCompany;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForCompany(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addInverseOneToOne(
            Company::getDoctrineStaticMeta()->getSingular(),
            Company::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
