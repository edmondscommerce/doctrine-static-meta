<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Person\Traits\HasPerson;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Person as Person;
use My\Test\Project\Entity\Relations\Person\Traits\HasPersonAbstract;

/**
 * Trait HasPersonUnidirectionalOneToOne
 *
 * One of the Current Entity relates to One Person
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\Person\Traits\HasPerson
 */
// phpcs:enable
trait HasPersonUnidirectionalOneToOne
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
        $builder->addOwningOneToOne(
            Person::getDoctrineStaticMeta()->getSingular(),
            Person::class
        );
    }
}
