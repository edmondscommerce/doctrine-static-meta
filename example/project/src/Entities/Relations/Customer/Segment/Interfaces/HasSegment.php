<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Segment\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Segment;

interface HasSegment
{
    public static function getPropertyMetaForSegment(ClassMetadataBuilder $builder);

    public function getSegment(): ?Segment;

    public function setSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface;

    public function removeSegment(): UsesPHPMetaDataInterface;

}
