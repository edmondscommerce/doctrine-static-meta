<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Traits;

use Doctrine\Common\PropertyChangedListener;

/**
 * Trait ImplementNotifyChangeTrackingPolicy
 *
 * @see     https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/change-tracking-policies.html#notify
 *
 * @package EdmondsCommerce\DoctrineStaticMeta\Entity\Traits
 */
trait ImplementNotifyChangeTrackingPolicy
{
    /**
     * @var array PropertyChangedListener[]
     */
    private $notifyChangeTrackingListeners = [];

    /**
     * @param PropertyChangedListener $listener
     */
    public function addPropertyChangedListener(PropertyChangedListener $listener)
    {
        $this->notifyChangeTrackingListeners[] = $listener;
    }

    /**
     * To be called from all set methods
     *
     * @param string $propName
     * @param        $newValue
     */
    private function updatePropertyValueAndNotify(string $propName, $newValue)
    {
        if ($this->$propName === $newValue) {
            return;
        }
        $oldValue        = $this->$propName;
        $this->$propName = $newValue;
        foreach ($this->notifyChangeTrackingListeners as $listener) {
            $listener->propertyChanged($this, $propName, $oldValue, $newValue);
        }
    }
}
