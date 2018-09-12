<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Person\Traits\HasPerson;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Person as Person;
use My\Test\Project\Entity\Relations\Person\Traits\HasPersonAbstract;
use My\Test\Project\Entity\Relations\Person\Traits\ReciprocatesPerson;

/**
 * Trait HasPersonOwningOneToOne
 *
 * The owning side of a One to One relationship between the Current Entity
 * and Person
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\Person\Traits\HasPerson
 */
// phpcs:enable
trait HasPersonOwningOneToOne
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
        $builder->addOwningOneToOne(
            Person::getDoctrineStaticMeta()->getSingular(),
            Person::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
