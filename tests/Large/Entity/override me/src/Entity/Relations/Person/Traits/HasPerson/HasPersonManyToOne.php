<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Person\Traits\HasPerson;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Person as Person;
use My\Test\Project\Entity\Relations\Person\Traits\HasPersonAbstract;
use My\Test\Project\Entity\Relations\Person\Traits\ReciprocatesPerson;

/**
 * Trait HasPersonManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of Person.
 *
 * Person has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\Person\HasPerson
 */
// phpcs:enable
trait HasPersonManyToOne
{
    use HasPersonAbstract;

    use ReciprocatesPerson;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForPerson(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addManyToOne(
            Person::getDoctrineStaticMeta()->getSingular(),
            Person::class,
            self::getDoctrineStaticMeta()->getPlural()
        );
    }
}
