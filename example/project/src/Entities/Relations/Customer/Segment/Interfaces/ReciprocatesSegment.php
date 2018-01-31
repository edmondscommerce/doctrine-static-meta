<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Segment\Interfaces;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Segment;

interface ReciprocatesSegment
{
    public function reciprocateRelationOnSegment(Segment $segment): UsesPHPMetaDataInterface;

    public function removeRelationOnSegment(Segment $segment): UsesPHPMetaDataInterface;
}
