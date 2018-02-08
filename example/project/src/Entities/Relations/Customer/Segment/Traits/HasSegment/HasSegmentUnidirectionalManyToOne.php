<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegment;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use  My\Test\Project\Entities\Relations\Customer\Segment\Traits\HasSegmentAbstract;
use My\Test\Project\Entities\Customer\Segment;

/**
 * Trait HasSegmentManyToOne
 *
 * ManyToOne - Many instances of the current Entity refer to One instance of the refered Entity.
 *
 * @package My\Test\Project\Entities\Traits\Relations\Segment\HasSegment
 */
trait HasSegmentUnidirectionalManyToOne
{
    use HasSegmentAbstract;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @throws \EdmondsCommerce\DoctrineStaticMeta\Exception\DoctrineStaticMetaException
     */
    public static function getPropertyMetaForSegment(ClassMetadataBuilder $builder): void
    {
        $builder->addManyToOne(
            Segment::getSingular(),
            Segment::class
        );
    }
}
