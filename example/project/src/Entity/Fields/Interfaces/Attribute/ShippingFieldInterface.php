<?php declare(strict_types=1);

namespace My\Test\Project\Entity\Fields\Interfaces\Attribute;

interface ShippingFieldInterface
{
    public const PROP_SHIPPING = 'shipping';

    public function getShipping(): float;

    public function setShipping(float $shipping);
}
