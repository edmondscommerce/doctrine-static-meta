<?php declare(strict_types=1);
// phpcs:disable

namespace My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Attributes\Address as AttributesAddress;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddressAbstract;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\ReciprocatesAttributesAddress;

/**
 * Trait HasAttributesAddressInverseOneToOne
 *
 * The inverse side of a One to One relationship between the Current Entity
 * and AttributesAddress
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\AttributesAddress\Traits\HasAttributesAddress
 */
// phpcs:enable
trait HasAttributesAddressInverseOneToOne
{
    use HasAttributesAddressAbstract;

    use ReciprocatesAttributesAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForAttributesAddress(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addInverseOneToOne(
            AttributesAddress::getDoctrineStaticMeta()->getSingular(),
            AttributesAddress::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
