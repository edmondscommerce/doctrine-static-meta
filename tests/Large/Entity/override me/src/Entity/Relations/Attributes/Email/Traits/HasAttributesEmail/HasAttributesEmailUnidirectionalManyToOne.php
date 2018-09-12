<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasAttributesEmail;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Attributes\Email as AttributesEmail;
use My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasAttributesEmailAbstract;



/**
 * Trait HasAttributesEmailManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance
 * of AttributesEmail
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\AttributesEmail\HasAttributesEmail
 */
// phpcs:enable
trait HasAttributesEmailUnidirectionalManyToOne
{
    use HasAttributesEmailAbstract;

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
            AttributesEmail::class
        );
    }
}
