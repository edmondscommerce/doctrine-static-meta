<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\Common\PropertyChangedListener;

interface ImplementNotifyChangeTrackingPolicyInterface extends NotifyPropertyChanged
{
    public function addPropertyChangedListener(PropertyChangedListener $listener): void;

    public function notifyEmbeddablePrefixedProperties(
        string $embeddablePropertyName,
        ?string $propName = null,
        $oldValue = null,
        $newValue = null
    ): void;
}
