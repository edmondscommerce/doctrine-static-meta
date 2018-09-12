<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasAttributesEmails;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Attributes\Email as AttributesEmail;
use My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasAttributesEmailsAbstract;
use My\Test\Project\Entity\Relations\Attributes\Email\Traits\ReciprocatesAttributesEmail;

/**
 * Trait HasAttributesEmailsInverseManyToMany
 *
 * The inverse side of a Many to Many relationship between the Current Entity
 * And AttributesEmail
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#owning-and-inverse-side-on-a-manytomany-association
 *
 * @package Test\Code\Generator\Entity\Relations\AttributesEmail\Traits\HasAttributesEmails
 */
// phpcs:enable
trait HasAttributesEmailsInverseManyToMany
{
    use HasAttributesEmailsAbstract;

    use ReciprocatesAttributesEmail;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForAttributesEmails(
        ClassMetadataBuilder $builder
    ): void {
        $manyToManyBuilder = $builder->createManyToMany(
            AttributesEmail::getDoctrineStaticMeta()->getPlural(),
            AttributesEmail::class
        );
        $manyToManyBuilder->mappedBy(self::getDoctrineStaticMeta()->getPlural());
        $fromTableName = Inflector::tableize(AttributesEmail::getDoctrineStaticMeta()->getPlural());
        $toTableName   = Inflector::tableize(self::getDoctrineStaticMeta()->getPlural());
        $manyToManyBuilder->setJoinTable($fromTableName . '_to_' . $toTableName);
        $manyToManyBuilder->addJoinColumn(
            Inflector::tableize(self::getDoctrineStaticMeta()->getSingular() . '_' . static::PROP_ID),
            static::PROP_ID
        );
        $manyToManyBuilder->addInverseJoinColumn(
            Inflector::tableize(
                AttributesEmail::getDoctrineStaticMeta()->getSingular() . '_' . AttributesEmail::PROP_ID
            ),
            AttributesEmail::PROP_ID
        );
        $manyToManyBuilder->build();
    }
}
