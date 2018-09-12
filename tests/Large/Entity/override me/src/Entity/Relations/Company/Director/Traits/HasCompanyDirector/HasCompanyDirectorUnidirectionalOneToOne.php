<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirector;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Company\Director as CompanyDirector;
use My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirectorAbstract;

/**
 * Trait HasCompanyDirectorUnidirectionalOneToOne
 *
 * One of the Current Entity relates to One CompanyDirector
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\CompanyDirector\Traits\HasCompanyDirector
 */
// phpcs:enable
trait HasCompanyDirectorUnidirectionalOneToOne
{
    use HasCompanyDirectorAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @throws \ReflectionException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function metaForCompanyDirector(
        ClassMetadataBuilder $builder
    ): void {
        $builder->addOwningOneToOne(
            CompanyDirector::getDoctrineStaticMeta()->getSingular(),
            CompanyDirector::class
        );
    }
}
