<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirectors;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Company\Director as CompanyDirector;
use My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirectorsAbstract;
use My\Test\Project\Entity\Relations\Company\Director\Traits\ReciprocatesCompanyDirector;

/**
 * Trait HasCompanyDirectorsOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to CompanyDirector.
 *
 * The CompanyDirector has a corresponding ManyToOne relationship
 * to the current Entity (that is using this trait)
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\CompanyDirector\HasCompanyDirectors
 */
// phpcs:enable
trait HasCompanyDirectorsOneToMany
{
    use HasCompanyDirectorsAbstract;

    use ReciprocatesCompanyDirector;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForCompanyDirectors(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOneToMany(
            CompanyDirector::getDoctrineStaticMeta()->getPlural(),
            CompanyDirector::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
