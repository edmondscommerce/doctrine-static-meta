<?php declare(strict_types=1);

namespace My\Test\Project\EntityRelations\Customer\Segment\Traits;

use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use  My\Test\Project\EntityRelations\Customer\Segment\Interfaces\ReciprocatesSegment;
use My\Test\Project\Entities\Customer\Segment;

trait HasSegmentAbstract
{
    /**
     * @var Segment|null
     */
    private $segment;

    /**
     * @param ClassMetadataBuilder $builder
     *
     * @return void
     */
    abstract public static function getPropertyMetaForSegment(ClassMetadataBuilder $builder): void;

    /**
     * @return Segment|null
     */
    public function getSegment(): ?Segment
    {
        return $this->segment;
    }

    /**
     * @param Segment $segment
     * @param bool           $recip
     *
     * @return $this|UsesPHPMetaDataInterface
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function setSegment(Segment $segment, bool $recip = true): UsesPHPMetaDataInterface
    {
        if ($this instanceof ReciprocatesSegment && true === $recip) {
            $this->reciprocateRelationOnSegment($segment);
        }
        $this->segment = $segment;

        return $this;
    }

    /**
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeSegment(): UsesPHPMetaDataInterface
    {
        $this->segment = null;

        return $this;
    }
}
