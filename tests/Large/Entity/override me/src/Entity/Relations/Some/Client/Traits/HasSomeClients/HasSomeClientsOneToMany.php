<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Some\Client\Traits\HasSomeClients;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Some\Client as SomeClient;
use My\Test\Project\Entity\Relations\Some\Client\Traits\HasSomeClientsAbstract;
use My\Test\Project\Entity\Relations\Some\Client\Traits\ReciprocatesSomeClient;

/**
 * Trait HasSomeClientsOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to SomeClient.
 *
 * The SomeClient has a corresponding ManyToOne relationship
 * to the current Entity (that is using this trait)
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\SomeClient\HasSomeClients
 */
// phpcs:enable
trait HasSomeClientsOneToMany
{
    use HasSomeClientsAbstract;

    use ReciprocatesSomeClient;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForSomeClients(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOneToMany(
            SomeClient::getDoctrineStaticMeta()->getPlural(),
            SomeClient::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
