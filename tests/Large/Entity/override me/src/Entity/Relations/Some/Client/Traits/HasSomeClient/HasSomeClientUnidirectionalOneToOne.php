<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Some\Client\Traits\HasSomeClient;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Some\Client as SomeClient;
use My\Test\Project\Entity\Relations\Some\Client\Traits\HasSomeClientAbstract;

/**
 * Trait HasSomeClientUnidirectionalOneToOne
 *
 * One of the Current Entity relates to One SomeClient
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\SomeClient\Traits\HasSomeClient
 */
// phpcs:enable
trait HasSomeClientUnidirectionalOneToOne
{
    use HasSomeClientAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForSomeClient(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOwningOneToOne(
            SomeClient::getDoctrineStaticMeta()->getSingular(),
            SomeClient::class
        );
    }
}
