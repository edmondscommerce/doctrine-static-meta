<?php declare(strict_types=1);
// phpcs:disable

namespace My\Test\Project\Entity\Relations\Some\Client\Traits\HasSomeClient;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Some\Client as SomeClient;
use My\Test\Project\Entity\Relations\Some\Client\Traits\HasSomeClientAbstract;
use My\Test\Project\Entity\Relations\Some\Client\Traits\ReciprocatesSomeClient;

/**
 * Trait HasSomeClientInverseOneToOne
 *
 * The inverse side of a One to One relationship between the Current Entity
 * and SomeClient
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\SomeClient\Traits\HasSomeClient
 */
// phpcs:enable
trait HasSomeClientInverseOneToOne
{
    use HasSomeClientAbstract;

    use ReciprocatesSomeClient;

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
        $builder->addInverseOneToOne(
            SomeClient::getDoctrineStaticMeta()->getSingular(),
            SomeClient::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
