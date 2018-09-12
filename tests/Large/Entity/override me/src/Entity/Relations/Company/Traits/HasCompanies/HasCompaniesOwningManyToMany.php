<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Company\Traits\HasCompanies;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Company as Company;
use My\Test\Project\Entity\Relations\Company\Traits\HasCompaniesAbstract;
use My\Test\Project\Entity\Relations\Company\Traits\ReciprocatesCompany;

/**
 * Trait HasCompaniesOwningManyToMany
 *
 * The owning side of a Many to Many relationship between the Current Entity
 * and Company
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package Test\Code\Generator\Entity\Relations\Company\Traits\HasCompanies
 */
// phpcs:enable
trait HasCompaniesOwningManyToMany
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

        $manyToManyBuilder = $builder->createManyToMany(
            Company::getDoctrineStaticMeta()->getPlural(),
            Company::class
        );
        $manyToManyBuilder->inversedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(Company::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                Company::getDoctrineStaticMeta()->getSingular() . '_' . Company::PROP_ID
            ),
            Company::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
