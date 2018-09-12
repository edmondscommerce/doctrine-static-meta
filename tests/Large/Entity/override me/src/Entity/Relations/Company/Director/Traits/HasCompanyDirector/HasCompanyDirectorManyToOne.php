<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirector;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Company\Director as CompanyDirector;
use My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirectorAbstract;
use My\Test\Project\Entity\Relations\Company\Director\Traits\ReciprocatesCompanyDirector;

/**
 * Trait HasCompanyDirectorManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of CompanyDirector.
 *
 * CompanyDirector has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\CompanyDirector\HasCompanyDirector
 */
// phpcs:enable
trait HasCompanyDirectorManyToOne
{
    use HasCompanyDirectorAbstract;

    use ReciprocatesCompanyDirector;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForCompanyDirector(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addManyToOne(
            CompanyDirector::getDoctrineStaticMeta()->getSingular(),
            CompanyDirector::class,
            self::getDoctrineStaticMeta()->getPlural()
        );
    }
}
