<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Segment\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Segment;
use  My\Test\Project\EntityRelations\Customer\Segment\Interfaces\ReciprocatesSegment;

trait HasSegmentsAbstract
{
    /**
     * @var ArrayCollection|Segment[]
     */
    private $segments;

    /**
     * @param ClassMetadataBuilder $manyToManyBuilder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForSegments(ClassMetadataBuilder $manyToManyBuilder): void;

    /**
     * @return Collection|Segment[]
     */
    public function getSegments(): Collection
    {
        return $this->segments;
    }

    /**
     * @param Collection|Segment[] $segments
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function setSegments(Collection $segments): UsesPHPMetaDataInterface
    {
        $this->segments = $segments;

        return $this;
    }

    /**
     * @param Segment $segment
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function addSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->segments->contains($segment)) {
            $this->segments->add($segment);
            if ($this instanceof ReciprocatesSegment && true === $recip) {
                $this->reciprocateRelationOnSegment($segment);
            }
        }

        return $this;
    }

    /**
     * @param Segment $segment
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function removeSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->segments->removeElement($segment);
        if ($this instanceof ReciprocatesSegment && true === $recip) {
            $this->removeRelationOnSegment($segment);
        }

        return $this;
    }

    /**
     * Initialise the segments property as a Doctrine ArrayCollection
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initSegments()
    {
        $this->segments = new ArrayCollection();

        return $this;
    }
}
