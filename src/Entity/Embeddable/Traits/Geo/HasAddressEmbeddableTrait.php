<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Embeddable\Traits\Geo;


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


    private function initAddress()
    {
        $this->addressEmbeddable = new AddressEmbeddable();
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
     * @return HasAddressEmbeddableInterface
     */
    public function setAddressEmbeddable(AddressEmbeddableInterface $address): HasAddressEmbeddableInterface
    {
        $this->addressEmbeddable = $address;

        return $this;
    }

    /**
     * @param ClassMetadataBuilder $builder
     */
    protected static function metaForAddress(ClassMetadataBuilder $builder): void
    {
        $builder->createEmbedded(HasAddressEmbeddableInterface::PROP_ADDRESS, AddressEmbeddable::class)
                ->setColumnPrefix(HasAddressEmbeddableInterface::COLUMN_PREFIX_ADDRESS)
                ->build();
    }
}
