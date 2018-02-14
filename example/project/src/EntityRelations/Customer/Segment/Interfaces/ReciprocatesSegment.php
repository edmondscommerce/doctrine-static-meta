<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Segment\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Segment;

interface ReciprocatesSegment
{
    /**
     * @param Segment $segment
     *
     * @return UsesPHPMetaDataInterface
     */
    public function reciprocateRelationOnSegment(Segment $segment): UsesPHPMetaDataInterface;

    /**
     * @param Segment $segment
     *
     * @return UsesPHPMetaDataInterface
     */
    public function removeRelationOnSegment(Segment $segment): UsesPHPMetaDataInterface;
}
