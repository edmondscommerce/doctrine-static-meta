<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasAttributesEmail;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Attributes\Email as AttributesEmail;
use My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasAttributesEmailAbstract;

/**
 * Trait HasAttributesEmailUnidirectionalOneToOne
 *
 * One of the Current Entity relates to One AttributesEmail
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\AttributesEmail\Traits\HasAttributesEmail
 */
// phpcs:enable
trait HasAttributesEmailUnidirectionalOneToOne
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
        $builder->addOwningOneToOne(
            AttributesEmail::getDoctrineStaticMeta()->getSingular(),
            AttributesEmail::class
        );
    }
}
