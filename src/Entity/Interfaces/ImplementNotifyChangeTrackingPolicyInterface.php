<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Interfaces;

use Doctrine\Common\NotifyPropertyChanged;
use Doctrine\Common\PropertyChangedListener;
use Doctrine\ORM\EntityManagerInterface;

interface ImplementNotifyChangeTrackingPolicyInterface extends NotifyPropertyChanged
{
    public function addPropertyChangedListener(PropertyChangedListener $listener): void;

    public function removePropertyChangedListeners();

    public function notifyEmbeddablePrefixedProperties(
        string $embeddablePropertyName,
        ?string $propName = null,
        $oldValue = null,
        $newValue = null
    ): void;

    public function ensureMetaDataIsSet(EntityManagerInterface $entityManager): void;
}
