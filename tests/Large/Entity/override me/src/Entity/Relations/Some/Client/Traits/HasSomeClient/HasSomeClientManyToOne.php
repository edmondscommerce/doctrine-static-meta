<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Some\Client\Traits\HasSomeClient;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Some\Client as SomeClient;
use My\Test\Project\Entity\Relations\Some\Client\Traits\HasSomeClientAbstract;
use My\Test\Project\Entity\Relations\Some\Client\Traits\ReciprocatesSomeClient;

/**
 * Trait HasSomeClientManyToOne
 *
 * ManyToOne - Many instances of the current Entity (that is using this trait) refer to
 *             One instance of SomeClient.
 *
 * SomeClient has a corresponding OneToMany relationship to the current
 * Entity (that is using this trait)
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-many-bidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\SomeClient\HasSomeClient
 */
// phpcs:enable
trait HasSomeClientManyToOne
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
        $builder->addManyToOne(
            SomeClient::getDoctrineStaticMeta()->getSingular(),
            SomeClient::class,
            self::getDoctrineStaticMeta()->getPlural()
        );
    }
}
