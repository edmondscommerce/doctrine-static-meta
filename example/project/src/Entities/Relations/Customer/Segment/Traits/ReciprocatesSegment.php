<?php declare(strict_types=1);

namespace My\Test\Project\Entities\Relations\Customer\Segment\Traits;

use EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces\UsesPHPMetaDataInterface;
use My\Test\Project\Entities\Customer\Segment;

trait ReciprocatesSegment
{
    /**
     * This method needs to set the relationship on the segment to this entity.
     *
     * It can be either plural or singular and so set or add as a method name respectively
     *
     * @param Segment $segment
     *
     * @return $this||UsesPHPMetaData
     */
    public function reciprocateRelationOnSegment(Segment $segment): UsesPHPMetaDataInterface
    {
        $singular = static::getSingular();
        $method   = 'add' . $singular;
        if (!method_exists($segment, $method)) {
            $method = 'set' . $singular;
        }

        $segment->$method($this, false);

        return $this;
    }

    /**
     * This method needs to remove the relationship on the segment to this entity.
     *
     * @param Segment $segment
     *
     * @return $this|UsesPHPMetaDataInterface
     */
    public function removeRelationOnSegment(Segment $segment): UsesPHPMetaDataInterface
    {
        $method = 'remove' . static::getSingular();
        $segment->$method($this, false);

        return $this;
    }

}
