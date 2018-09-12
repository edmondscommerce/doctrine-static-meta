<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Person\Traits\HasPerson;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Person as Person;
use My\Test\Project\Entity\Relations\Person\Traits\HasPersonAbstract;



/**
 * Trait HasPersonManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance
 * of Person
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\Person\HasPerson
 */
// phpcs:enable
trait HasPersonUnidirectionalManyToOne
{
    use HasPersonAbstract;

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
            Person::class
        );
    }
}
