<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Person\Traits\HasPeople;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Person as Person;
use My\Test\Project\Entity\Relations\Person\Traits\HasPeopleAbstract;
use My\Test\Project\Entity\Relations\Person\Traits\ReciprocatesPerson;

/**
 * Trait HasPeopleInverseManyToMany
 *
 * The inverse side of a Many to Many relationship between the Current Entity
 * And Person
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package Test\Code\Generator\Entity\Relations\Person\Traits\HasPeople
 */
// phpcs:enable
trait HasPeopleInverseManyToMany
{
    use HasPeopleAbstract;

    use ReciprocatesPerson;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForPeople(
        ClassMetadataBuilder $builder
    ): void {
        $manyToManyBuilder = $builder->createManyToMany(
            Person::getDoctrineStaticMeta()->getPlural(),
            Person::class
        );
        $manyToManyBuilder->mappedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(Person::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                Person::getDoctrineStaticMeta()->getSingular() . '_' . Person::PROP_ID
            ),
            Person::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
