<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Company\Traits\HasCompanies;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Company as Company;
use My\Test\Project\Entity\Relations\Company\Traits\HasCompaniesAbstract;
use My\Test\Project\Entity\Relations\Company\Traits\ReciprocatesCompany;

/**
 * Trait HasCompaniesOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to Company.
 *
 * The Company has a corresponding ManyToOne relationship
 * to the current Entity (that is using this trait)
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\Company\HasCompanies
 */
// phpcs:enable
trait HasCompaniesOneToMany
{
    use HasCompaniesAbstract;

    use ReciprocatesCompany;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForCompanies(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOneToMany(
            Company::getDoctrineStaticMeta()->getPlural(),
            Company::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
