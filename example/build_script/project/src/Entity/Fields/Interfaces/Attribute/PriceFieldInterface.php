<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Attribute;

interface PriceFieldInterface
{
    public const PROP_PRICE = 'price';

    public function getPrice(): float;

    public function setPrice(float $price);
}
