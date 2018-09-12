<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddress;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Attributes\Address as AttributesAddress;
use My\Test\Project\Entity\Relations\Attributes\Address\Traits\HasAttributesAddressAbstract;

/**
 * Trait HasAttributesAddressUnidirectionalOneToOne
 *
 * One of the Current Entity relates to One AttributesAddress
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\AttributesAddress\Traits\HasAttributesAddress
 */
// phpcs:enable
trait HasAttributesAddressUnidirectionalOneToOne
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
        $builder->addOwningOneToOne(
            AttributesAddress::getDoctrineStaticMeta()->getSingular(),
            AttributesAddress::class
        );
    }
}
