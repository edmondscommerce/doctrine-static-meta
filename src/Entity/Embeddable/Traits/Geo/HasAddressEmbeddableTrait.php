<?php declare(strict_types=1);

namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo;

use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Geo\HasAddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Interfaces\Objects\Geo\AddressEmbeddableInterface;
use EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Objects\Geo\AddressEmbeddable;

trait HasAddressEmbeddableTrait
{
    /**
     * @var AddressEmbeddableInterface
     */
    private $addressEmbeddable;

    /**
     * Called at construction time
     */
    private function initEmbeddableAddress(): void
    {
        $this->addressEmbeddable = new AddressEmbeddable();
        $this->addressEmbeddable->setOwningEntity($this);
    }

    /**
     * @return AddressEmbeddableInterface
     */
    public function getAddressEmbeddable(): AddressEmbeddableInterface
    {
        return $this->addressEmbeddable;
    }

    /**
     * @param AddressEmbeddableInterface $address
     *
     * @return $this
     */
    public function setAddressEmbeddable(AddressEmbeddableInterface $address): self
    {
        $this->addressEmbeddable = $address;

        return $this;
    }

    public function postLoadSetOwningEntityOnAddressEmbeddable(): void
    {
        $this->addressEmbeddable->setOwningEntity($this);
    }

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function metaForAddress(ClassMetadataBuilder $builder): void
    {
        $builder->addLifecycleEvent('postLoadSetOwningEntityOnAddressEmbeddable', Events::postLoad);
        $builder->createEmbedded(
            HasAddressEmbeddableInterface::PROP_ADDRESS_EMBEDDABLE,
            AddressEmbeddable::class
        )
                ->setColumnPrefix(
                    HasAddressEmbeddableInterface::COLUMN_PREFIX_ADDRESS
                )
                ->build();
    }
}
