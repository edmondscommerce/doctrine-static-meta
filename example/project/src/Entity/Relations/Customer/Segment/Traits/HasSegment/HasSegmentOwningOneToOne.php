<?php declare(strict_types=1);


namespace My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasSegment;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use My\Test\Project\Entity\Relations\Customer\Segment\Traits\ReciprocatesSegment;
use My\Test\Project\Entities\Customer\Segment;
use My\Test\Project\Entity\Relations\Customer\Segment\Traits\HasSegmentAbstract;

trait HasSegmentOwningOneToOne
{
    use HasSegmentAbstract;

    use ReciprocatesSegment;

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
            Segment::class,
            static::getSingular()
        );
    }
}
