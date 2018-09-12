<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Attributes\Address as AttributesAddress;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddressAbstract;



/**
 * Trait HasAttributesAddressManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance
 * of AttributesAddress
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\AttributesAddress\HasAttributesAddress
 */
// phpcs:enable
trait HasAttributesAddressUnidirectionalManyToOne
{
    use HasAttributesAddressAbstract;

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
            AttributesAddress::class
        );
    }
}
