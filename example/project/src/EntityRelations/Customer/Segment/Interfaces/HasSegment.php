<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Segment\Interfaces;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Segment;

interface HasSegment
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForSegment(ClassMetadataBuilder $builder): void;

    /**
     * @return null|Segment
     */
    public function getSegment(): ?Segment;

    /**
     * @param Segment $segment
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @return UsesPHPMetaDataInterface
     */
    public function removeSegment(): UsesPHPMetaDataInterface;
}
