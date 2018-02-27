<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasSegment;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasSegmentAbstract;
use My\Test\Project\Entities\Customer\Segment;

trait HasSegmentUnidirectionalOneToOne
{
    use HasSegmentAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public static function getPropertyDoctrineMetaForSegment(ClassMetadataBuilder $builder): void
    {
        $builder->addOwningOneToOne(
            Segment::getSingular(),
            Segment::class
        );
    }
}
