<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order\Address as OrderAddress;
use My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddressAbstract;

/**
 * Trait HasOrderAddressUnidirectionalOneToOne
 *
 * One of the Current Entity relates to One OrderAddress
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\OrderAddress\Traits\HasOrderAddress
 */
// phpcs:enable
trait HasOrderAddressUnidirectionalOneToOne
{
    use HasOrderAddressAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForOrderAddress(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOwningOneToOne(
            OrderAddress::getDoctrineStaticMeta()->getSingular(),
            OrderAddress::class
        );
    }
}
