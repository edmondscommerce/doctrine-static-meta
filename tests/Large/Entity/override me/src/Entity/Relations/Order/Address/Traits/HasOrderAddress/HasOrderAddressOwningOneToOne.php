<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Order\Address as OrderAddress;
use My\Test\Project\Entity\Relations\Order\Address\Traits\HasOrderAddressAbstract;
use My\Test\Project\Entity\Relations\Order\Address\Traits\ReciprocatesOrderAddress;

/**
 * Trait HasOrderAddressOwningOneToOne
 *
 * The owning side of a One to One relationship between the Current Entity
 * and OrderAddress
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\OrderAddress\Traits\HasOrderAddress
 */
// phpcs:enable
trait HasOrderAddressOwningOneToOne
{
    use HasOrderAddressAbstract;

    use ReciprocatesOrderAddress;

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
            OrderAddress::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
