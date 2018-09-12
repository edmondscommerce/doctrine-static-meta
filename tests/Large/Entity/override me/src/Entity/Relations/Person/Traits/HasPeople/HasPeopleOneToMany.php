<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Person\Traits\HasPeople;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Person as Person;
use My\Test\Project\Entity\Relations\Person\Traits\HasPeopleAbstract;
use My\Test\Project\Entity\Relations\Person\Traits\ReciprocatesPerson;

/**
 * Trait HasPeopleOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to Person.
 *
 * The Person has a corresponding ManyToOne relationship
 * to the current Entity (that is using this trait)
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\Person\HasPeople
 */
// phpcs:enable
trait HasPeopleOneToMany
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
        $builder->addOneToMany(
            Person::getDoctrineStaticMeta()->getPlural(),
            Person::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
