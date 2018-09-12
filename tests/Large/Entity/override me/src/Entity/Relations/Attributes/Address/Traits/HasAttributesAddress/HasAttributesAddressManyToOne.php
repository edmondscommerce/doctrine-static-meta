<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Attributes\Address as AttributesAddress;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddressAbstract;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\ReciprocatesAttributesAddress;

/**
 * Trait HasAttributesAddressManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of AttributesAddress.
 *
 * AttributesAddress has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\AttributesAddress\HasAttributesAddress
 */
// phpcs:enable
trait HasAttributesAddressManyToOne
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
        $builder->addManyToOne(
            AttributesAddress::getDoctrineStaticMeta()->getSingular(),
            AttributesAddress::class,
            self::getDoctrineStaticMeta()->getPlural()
        );
    }
}
