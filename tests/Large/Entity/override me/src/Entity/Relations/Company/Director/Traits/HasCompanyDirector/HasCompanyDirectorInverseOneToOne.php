<?php declare(strict_types=1);
// phpcs:disable

namespace My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirector;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entities\Company\Director as CompanyDirector;
use My\Test\Project\Entity\Relations\Company\Director\Traits\HasCompanyDirectorAbstract;
use My\Test\Project\Entity\Relations\Company\Director\Traits\ReciprocatesCompanyDirector;

/**
 * Trait HasCompanyDirectorInverseOneToOne
 *
 * The inverse side of a One to One relationship between the Current Entity
 * and CompanyDirector
 *
 * @see https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/association-mapping.html#one-to-one-bidirectional
 *
 * @package Test\Code\Generator\Entity\Relations\CompanyDirector\Traits\HasCompanyDirector
 */
// phpcs:enable
trait HasCompanyDirectorInverseOneToOne
{
    use HasCompanyDirectorAbstract;

    use ReciprocatesCompanyDirector;

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
        $builder->addInverseOneToOne(
            CompanyDirector::getDoctrineStaticMeta()->getSingular(),
            CompanyDirector::class,
            self::getDoctrineStaticMeta()->getSingular()
        );
    }
}
