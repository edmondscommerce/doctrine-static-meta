<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasAttributesEmail;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Attributes\Email as AttributesEmail;
use My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasAttributesEmailAbstract;
use My\Test\Project\Entity\Relations\Attributes\Email\Traits\ReciprocatesAttributesEmail;

/**
 * Trait HasAttributesEmailManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of AttributesEmail.
 *
 * AttributesEmail has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\AttributesEmail\HasAttributesEmail
 */
// phpcs:enable
trait HasAttributesEmailManyToOne
{
    use HasAttributesEmailAbstract;

    use ReciprocatesAttributesEmail;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForAttributesEmail(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addManyToOne(
            AttributesEmail::getDoctrineStaticMeta()->getSingular(),
            AttributesEmail::class,
            self::getDoctrineStaticMeta()->getPlural()
        );
    }
}
