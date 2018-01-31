<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Segment\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Segment;

trait HasSegmentsAbstract
{
    /**
     * @var ArrayCollection|Segment[]
     */
    private $segments;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForSegments(ClassMetadataBuilder $builder);

    /**
     * @return Collection|Segment[]
     */
    public function getSegments(): Collection
    {
        return $this->segments;
    }

    /**
     * @param Collection $segments
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
     */
    public function addSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface
    {
        if (!$this->segments->contains($segment)) {
            $this->segments->add($segment);
            if (true === $recip) {
                $this->reciprocateRelationOnSegment($segment, false);
            }
        }

        return $this;
    }

    /**
     * @param Segment $segment
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface
    {
        $this->segments->removeElement($segment);
        if (true === $recip) {
            $this->removeRelationOnSegment($segment, false);
        }

        return $this;
    }

    private function initSegments()
    {
        $this->segments = new ArrayCollection();

        return $this;
    }
}
