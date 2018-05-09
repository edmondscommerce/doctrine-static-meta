<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasCustomerSegment;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Customer\Segment\Traits\ReciprocatesCustomerSegment;
use My\Test\Project\Entities\Customer\Segment as CustomerSegment;
use My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasCustomerSegmentAbstract;

trait HasCustomerSegmentInverseOneToOne
{
    use HasCustomerSegmentAbstract;

    use ReciprocatesCustomerSegment;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForCustomerSegment(ClassMetadataBuilder $builder): void
    {
        $builder->addInverseOneToOne(
            CustomerSegment::getSingular(),
            CustomerSegment::class,
            static::getSingular()
        );
    }
}
