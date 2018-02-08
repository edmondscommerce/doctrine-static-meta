<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Segment\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Segment;

interface HasSegments
{
    public static function getPropertyMetaForSegments(ClassMetadataBuilder $builder);

    public function getSegments(): Collection;

    public function setSegments(Collection $segments): UsesPHPMetaDataInterface;

    public function addSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface;

}
