<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirectors;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Company\Director as CompanyDirector;
use My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirectorsAbstract;
use My\Test\Project\Entity\Relations\Company\Director\Traits\ReciprocatesCompanyDirector;

/**
 * Trait HasCompanyDirectorsInverseManyToMany
 *
 * The inverse side of a Many to Many relationship between the Current Entity
 * And CompanyDirector
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package Test\Code\Generator\Entity\Relations\CompanyDirector\Traits\HasCompanyDirectors
 */
// phpcs:enable
trait HasCompanyDirectorsInverseManyToMany
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
        $manyToManyBuilder = $builder->createManyToMany(
            CompanyDirector::getDoctrineStaticMeta()->getPlural(),
            CompanyDirector::class
        );
        $manyToManyBuilder->mappedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(CompanyDirector::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                CompanyDirector::getDoctrineStaticMeta()->getSingular() . '_' . CompanyDirector::PROP_ID
            ),
            CompanyDirector::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
