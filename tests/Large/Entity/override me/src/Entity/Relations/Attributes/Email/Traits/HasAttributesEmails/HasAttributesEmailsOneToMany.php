<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasAttributesEmails;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Attributes\Email as AttributesEmail;
use My\Test\Project\Entity\Relations\Attributes\Email\Traits\HasAttributesEmailsAbstract;
use My\Test\Project\Entity\Relations\Attributes\Email\Traits\ReciprocatesAttributesEmail;

/**
 * Trait HasAttributesEmailsOneToMany
 *
 * One instance of the current Entity (that is using this trait)
 * has Many instances (references) to AttributesEmail.
 *
 * The AttributesEmail has a corresponding ManyToOne relationship
 * to the current Entity (that is using this trait)
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\AttributesEmail\HasAttributesEmails
 */
// phpcs:enable
trait HasAttributesEmailsOneToMany
{
    use HasAttributesEmailsAbstract;

    use ReciprocatesAttributesEmail;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForAttributesEmails(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOneToMany(
            AttributesEmail::getDoctrineStaticMeta()->getPlural(),
            AttributesEmail::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
