<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddresses;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Attributes\Address as AttributesAddress;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddressesAbstract;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\ReciprocatesAttributesAddress;

/**
 * Trait HasAttributesAddressesOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to AttributesAddress.
 *
 * The AttributesAddress has a corresponding ManyToOne relationship
 * to the current Entity (that is using this trait)
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\AttributesAddress\HasAttributesAddresses
 */
// phpcs:enable
trait HasAttributesAddressesOneToMany
{
    use HasAttributesAddressesAbstract;

    use ReciprocatesAttributesAddress;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForAttributesAddresses(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOneToMany(
            AttributesAddress::getDoctrineStaticMeta()->getPlural(),
            AttributesAddress::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
