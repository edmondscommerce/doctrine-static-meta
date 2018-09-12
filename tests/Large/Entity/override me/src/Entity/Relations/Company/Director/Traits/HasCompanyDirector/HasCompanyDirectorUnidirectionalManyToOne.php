<?php declare(strict_types=1);
// phpcs:disable
namespace My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirector;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Company\Director as CompanyDirector;
use My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirectorAbstract;



/**
 * Trait HasCompanyDirectorManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance
 * of CompanyDirector
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#many-to-one-unidirectional
 *
 * @package Test\Code\Generator\Entities\Traits\Relations\CompanyDirector\HasCompanyDirector
 */
// phpcs:enable
trait HasCompanyDirectorUnidirectionalManyToOne
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
        $builder->addManyToOne(
            CompanyDirector::getDoctrineStaticMeta()->getSingular(),
            CompanyDirector::class
        );
    }
}
