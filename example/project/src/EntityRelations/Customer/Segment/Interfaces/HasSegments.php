<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Segment\Interfaces;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Segment;

interface HasSegments
{
    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    public static function getPropertyMetaForSegments(ClassMetadataBuilder $builder): void;

    /**
     * @return Collection|Segment[]
     */
    public function getSegments(): Collection;

    /**
     * @param Collection|Segment[] $segments
     *
     * @return UsesPHPMetaDataInterface
     */
    public function setSegments(Collection $segments): UsesPHPMetaDataInterface;

    /**
     * @param Segment $segment
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface;

    /**
     * @param Segment $segment
     * @param bool           $recip
     *
     * @return UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface;
}
