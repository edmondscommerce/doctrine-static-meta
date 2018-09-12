<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Company\Traits\HasCompany;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Company as Company;
use My\Test\Project\Entity\Relations\Company\Traits\HasCompanyAbstract;

/**
 * Trait HasCompanyUnidirectionalOneToOne
 *
 * One of the Current Entity relates to One Company
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\Company\Traits\HasCompany
 */
// phpcs:enable
trait HasCompanyUnidirectionalOneToOne
{
    use HasCompanyAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForCompany(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOwningOneToOne(
            Company::getDoctrineStaticMeta()->getSingular(),
            Company::class
        );
    }
}
