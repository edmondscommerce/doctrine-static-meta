<?php declare(strict_types=1);


namespace EdmondsCommerce\DoctrineStaticMeta\Entity\Fields\Interfaces\Financial;


interface PriceFieldInterface
{
    public const PROP_PRICE = 'price';

    public const DEFAULT_PRICE = 0.0;

    public function getPrice(): float;

    public function setPrice(float $price);
}
