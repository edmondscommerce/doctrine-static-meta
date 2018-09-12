<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Company\Traits\HasCompany;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Company as Company;
use My\Test\Project\Entity\Relations\Company\Traits\HasCompanyAbstract;
use My\Test\Project\Entity\Relations\Company\Traits\ReciprocatesCompany;

/**
 * Trait HasCompanyManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of Company.
 *
 * Company has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\Company\HasCompany
 */
// phpcs:enable
trait HasCompanyManyToOne
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
        $builder->addManyToOne(
            Company::getDoctrineStaticMeta()->getSingular(),
            Company::class,
            self::getDoctrineStaticMeta()->getPlural()
        );
    }
}
